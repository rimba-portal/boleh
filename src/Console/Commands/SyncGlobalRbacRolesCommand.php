<?php

declare(strict_types=1);

namespace Rimba\Can\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Rimba\Can\Services\RbacRoleSynchronizer;
use Rimba\Can\Support\RbacRoleName;

#[Description('Synchronize global Rimba RBAC roles.')]
#[Signature('rimba:rbac-sync-global')]
final class SyncGlobalRbacRolesCommand extends Command
{
    public function handle(RbacRoleSynchronizer $roles): int
    {
        $packageRoles = collect(
            config('bites.can.rbac.package_managers', []),
        )->map(
            static fn (string $package): string => strtolower(trim($package)).'_manager',
        );

        $globalRoles = collect(
            config('bites.can.rbac.global_roles', []),
        );

        $responsibilities = $packageRoles
            ->merge($globalRoles)
            ->filter(static fn (mixed $role): bool => is_string($role) && trim($role) !== ''
            )
            ->unique()
            ->sort()
            ->values()
            ->all();

        $synced = $roles->syncScope(
            namespace: RbacRoleName::JOB,
            scope: RbacRoleName::GLOBAL,
            responsibilities: $responsibilities,
        );

        $this->table(
            ['Role'],
            $synced
                ->map(static fn ($role): array => [$role->name])
                ->all(),
        );

        $this->components->info(sprintf(
            'Synchronized %d global RBAC role(s).',
            $synced->count(),
        ));

        return self::SUCCESS;
    }
}
