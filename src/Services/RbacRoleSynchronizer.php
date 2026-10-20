<?php

declare(strict_types=1);

namespace Rimba\Can\Services;

use Illuminate\Support\Collection;
use Rimba\Can\Support\RbacRoleName;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

final class RbacRoleSynchronizer
{
    /**
     * @param  array<int, string>  $responsibilities
     * @return Collection<int, Role>
     */
    public function syncScope(
        string $namespace,
        string $scope,
        array $responsibilities,
    ): Collection {
        $guard = $this->guardName();

        $roles = collect($responsibilities)
            ->map(
                fn (string $responsibility): string => RbacRoleName::make(
                    $namespace,
                    $scope,
                    $responsibility,
                )
            )
            ->unique()
            ->map(
                fn (string $name): Role => Role::findOrCreate($name, $guard)
            )
            ->values();

        $this->forgetCache();

        return $roles;
    }

    /**
     * @param  array<int, string>  $responsibilities
     */
    public function renameScope(
        string $namespace,
        string $oldScope,
        string $newScope,
        array $responsibilities,
    ): void {
        $guard = $this->guardName();

        foreach ($responsibilities as $responsibility) {
            $oldName = RbacRoleName::make(
                $namespace,
                $oldScope,
                $responsibility,
            );

            $newName = RbacRoleName::make(
                $namespace,
                $newScope,
                $responsibility,
            );

            if ($oldName === $newName) {
                continue;
            }

            $role = Role::query()
                ->where('guard_name', $guard)
                ->where('name', $oldName)
                ->first();

            if ($role === null) {
                Role::findOrCreate($newName, $guard);

                continue;
            }

            $targetExists = Role::query()
                ->where('guard_name', $guard)
                ->where('name', $newName)
                ->exists();

            if ($targetExists) {
                throw new \RuntimeException(
                    "Cannot rename RBAC role [{$oldName}] "
                    ."to [{$newName}] because the target role already exists."
                );
            }

            $role->forceFill([
                'name' => $newName,
            ])->save();
        }

        $this->forgetCache();
    }

    /**
     * @param  array<int, string>  $responsibilities
     */
    public function deleteScope(
        string $namespace,
        string $scope,
        array $responsibilities,
    ): void {
        $guard = $this->guardName();

        $names = collect($responsibilities)
            ->map(
                fn (string $responsibility): string => RbacRoleName::make(
                    $namespace,
                    $scope,
                    $responsibility,
                )
            )
            ->unique()
            ->values()
            ->all();

        Role::query()
            ->where('guard_name', $guard)
            ->whereIn('name', $names)
            ->delete();

        $this->forgetCache();
    }

    private function guardName(): string
    {
        return (string) config(
            'bites.can.sync.guard',
            'web',
        );
    }

    private function forgetCache(): void
    {
        app(PermissionRegistrar::class)
            ->forgetCachedPermissions();
    }
}
