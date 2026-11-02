<?php

declare(strict_types=1);

namespace Rimba\Can\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Rimba\Can\Support\RbacRoleName;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

final class GlobalRbacRoleBuilder
{
    private const MANAGED_PANELS = [
        'admin',
        'team',
    ];

    private const MANAGED_LEVELS = [
        'viewer' => [
            'viewAny',
            'view',
        ],
        'contributor' => [
            'viewAny',
            'view',
            'create',
            'update',
        ],
        'admin' => [
            'viewAny',
            'view',
            'create',
            'update',
            'delete',
            'restore',
            'forceDelete',
        ],
    ];

    private const STANDARD_LEVELS = [
        'viewer' => [
            'viewAny',
            'view',
        ],
        'contributor' => [
            'viewAny',
            'view',
            'create',
            'update',
        ],
        'moderator' => [
            'viewAny',
            'view',
            'update',
            'delete',
            'restore',
        ],
        'admin' => [
            'viewAny',
            'view',
            'create',
            'update',
            'delete',
            'restore',
            'forceDelete',
        ],
    ];

    public function sync(string $guard = 'web'): array
    {
        $permissions = Permission::query()
            ->where('guard_name', $guard)
            ->whereNotNull('domain')
            ->whereNotNull('resource')
            ->get();

        $result = [
            'created' => 0,
            'updated' => 0,
            'deleted' => 0,
            'roles' => [],
        ];

        DB::transaction(function () use (
            $permissions,
            $guard,
            &$result,
        ): void {
            $permissions
                ->groupBy(
                    fn (Permission $permission): string => $this->groupKey(
                        domain: (string) $permission->domain,
                        resource: (string) $permission->resource,
                    ),
                )
                ->each(function (Collection $group) use (
                    $guard,
                    &$result,
                ): void {
                    $this->syncPageRoles(
                        permissions: $group->where('type', 'page'),
                        guard: $guard,
                        result: $result,
                    );

                    $this->syncResourceRoles(
                        permissions: $group->where('type', 'resource'),
                        guard: $guard,
                        result: $result,
                    );
                });

            $result['deleted'] = $this->pruneManagedRoles(
                expectedNames: $result['roles'],
                guard: $guard,
            );
        });

        sort($result['roles']);

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        return $result;
    }

    private function syncPageRoles(
        Collection $permissions,
        string $guard,
        array &$result,
    ): void {
        foreach ($permissions as $permission) {
            $level = match ($permission->action) {
                'manage' => 'moderator',
                'access' => 'viewer',
                default => null,
            };

            if ($level === null) {
                continue;
            }

            $this->syncRole(
                domain: (string) $permission->domain,
                resource: (string) $permission->resource,
                level: $level,
                permissions: collect([$permission]),
                guard: $guard,
                result: $result,
            );
        }
    }

    private function syncResourceRoles(
        Collection $permissions,
        string $guard,
        array &$result,
    ): void {
        if ($permissions->isEmpty()) {
            return;
        }

        /** @var Permission $first */
        $first = $permissions->first();

        $levels = in_array(
            $first->panel,
            self::MANAGED_PANELS,
            true,
        )
            ? self::MANAGED_LEVELS
            : self::STANDARD_LEVELS;

        foreach ($levels as $level => $actions) {
            $selected = $permissions
                ->whereIn('action', $actions)
                ->values();

            if ($selected->isEmpty()) {
                continue;
            }

            $this->syncRole(
                domain: (string) $first->domain,
                resource: (string) $first->resource,
                level: $level,
                permissions: $selected,
                guard: $guard,
                result: $result,
            );
        }
    }

    private function syncRole(
        string $domain,
        string $resource,
        string $level,
        Collection $permissions,
        string $guard,
        array &$result,
    ): void {
        $name = $this->roleName(
            domain: $domain,
            resource: $resource,
            level: $level,
        );

        $role = Role::query()->firstOrCreate([
            'name' => $name,
            'guard_name' => $guard,
        ]);

        $created = $role->wasRecentlyCreated;

        $role->syncPermissions(
            $permissions
                ->pluck('name')
                ->values()
                ->all(),
        );

        if ($created) {
            $result['created']++;
        } else {
            $result['updated']++;
        }

        $result['roles'][] = $name;
    }

    private function roleName(
        string $domain,
        string $resource,
        string $level,
    ): string {
        return RbacRoleName::globalJob(
            implode('_', [
                $domain,
                $resource,
                $level,
            ]),
        );
    }

    private function groupKey(
        string $domain,
        string $resource,
    ): string {
        return $domain.'::'.$resource;
    }

    /**
     * Remove obsolete generated global roles.
     *
     * Package-manager roles are excluded because they end in "_manager"
     * and are not owned by this builder.
     *
     * @param  array<int, string>  $expectedNames
     */
    private function pruneManagedRoles(
        array $expectedNames,
        string $guard,
    ): int {
        $prefix = RbacRoleName::JOB
            .RbacRoleName::DELIMITER
            .RbacRoleName::GLOBAL
            .RbacRoleName::DELIMITER;

        $builder = Role::query()
            ->where('guard_name', $guard)
            ->where('name', 'like', $prefix.'%')
            ->where('name', 'not like', $prefix.'%_manager');

        if ($expectedNames === []) {
            return 0;
        }

        return $builder
            ->whereNotIn('name', array_values(array_unique($expectedNames)))
            ->delete();
    }
}
