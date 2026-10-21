<?php

declare(strict_types=1);

namespace Rimba\Can\Services;

use Illuminate\Support\Collection;
use RuntimeException;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

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
            ->whereNotNull('resource')
            ->get();

        $this->assertUniqueResources($permissions);

        $result = [
            'created' => 0,
            'updated' => 0,
            'roles' => [],
        ];

        $permissions
            ->groupBy('resource')
            ->each(function (Collection $permissions) use (
                $guard,
                &$result,
            ): void {
                $pages = $permissions->where('type', 'page');

                $resources = $permissions->where(
                    'type',
                    'resource',
                );

                $this->syncPageRoles(
                    permissions: $pages,
                    guard: $guard,
                    result: $result,
                );

                $this->syncResourceRoles(
                    permissions: $resources,
                    guard: $guard,
                    result: $result,
                );
            });

        sort($result['roles']);

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
                resource: $permission->resource,
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

        $panel = $permissions->first()->panel;

        $levels = in_array(
            $panel,
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
                resource: $permissions->first()->resource,
                level: $level,
                permissions: $selected,
                guard: $guard,
                result: $result,
            );
        }
    }

    private function syncRole(
        string $resource,
        string $level,
        Collection $permissions,
        string $guard,
        array &$result,
    ): void {
        $name = $this->roleName(
            resource: $resource,
            level: $level,
        );

        $role = Role::query()->firstOrCreate([
            'name' => $name,
            'guard_name' => $guard,
        ]);

        $created = $role->wasRecentlyCreated;

        $role->syncPermissions($permissions);

        if ($created) {
            $result['created']++;
        } else {
            $result['updated']++;
        }

        $result['roles'][] = $name;
    }

    private function roleName(
        string $resource,
        string $level,
    ): string {
        return sprintf(
            'job_roles◆global◆%s_%s',
            $resource,
            $level,
        );
    }

    private function assertUniqueResources(
        Collection $permissions,
    ): void {
        $duplicates = $permissions
            ->filter(
                fn (Permission $permission): bool => filled($permission->domain),
            )
            ->groupBy('resource')
            ->filter(
                fn (Collection $group): bool => $group
                    ->pluck('domain')
                    ->filter()
                    ->unique()
                    ->count() > 1,
            );

        if ($duplicates->isEmpty()) {
            return;
        }

        $resources = $duplicates
            ->keys()
            ->sort()
            ->implode(', ');

        throw new RuntimeException(
            "Global RBAC resource collision: {$resources}",
        );
    }
}
