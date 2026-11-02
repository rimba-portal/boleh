<?php

declare(strict_types=1);

namespace Rimba\Can\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Rimba\Can\Services\GlobalRbacRoleBuilder;

#[Description('Synchronize global RBAC roles from permission metadata')]
#[Signature('rimba:sync-global-rbac-roles
    {--guard=web : Permission guard}
')]
final class SyncGlobalRbacRolesCommand extends Command
{
    public function handle(
        GlobalRbacRoleBuilder $builder,
    ): int {
        $result = $builder->sync(
            guard: (string) $this->option('guard'),
        );

        $this->components->info(
            'Global RBAC roles synchronized.',
        );

        $this->table(
            ['Result', 'Count'],
            [
                ['Created', $result['created']],
                ['Updated', $result['updated']],
                ['Deleted', $result['deleted']],
                ['Roles', count($result['roles'])],
            ],
        );

        return self::SUCCESS;
    }
}
