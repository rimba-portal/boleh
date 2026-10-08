<?php

declare(strict_types=1);

namespace Rimba\Can\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Rimba\Can\Contracts\PermissionSynchronizer;
use Spatie\Permission\PermissionRegistrar;
use Throwable;

#[Description('Synchronize discovered permissions into Spatie permissions.')]
#[Signature('rimba:lock-sync
    {--prune : Remove permissions for the configured guard that are no longer discovered}
    {--force : Run destructive pruning without interactive confirmation}
')]
final class SyncPermissionsCommand extends BolehCommand
{
    public function handle(
        PermissionSynchronizer $synchronizer,
        PermissionRegistrar $permissionRegistrar,
    ): int {
        $definitions = $this->definitions();

        if (! $this->ensureDefinitionsWereDiscovered($definitions)) {
            return self::FAILURE;
        }

        $prune = (bool) $this->option('prune');

        if ($prune && ! $this->confirmPruning()) {
            $this->components->warn(
                'Permission synchronization was cancelled.',
            );

            return self::SUCCESS;
        }

        try {
            $result = $synchronizer->sync(
                definitions: $definitions,
                prune: $prune,
            );

            $permissionRegistrar->forgetCachedPermissions();
        } catch (Throwable $throwable) {
            report($throwable);

            $this->components->error(
                'Permission synchronization failed: '.$throwable->getMessage(),
            );

            return self::FAILURE;
        }

        $this->table(
            ['Metric', 'Count'],
            [
                ['Discovered', (int) ($result['total'] ?? count($definitions))],
                ['Created', (int) ($result['created'] ?? 0)],
                ['Updated', (int) ($result['updated'] ?? 0)],
                ['Deleted', (int) ($result['deleted'] ?? 0)],
            ],
        );

        $this->newLine();

        $this->components->info(sprintf(
            'Permission synchronization completed for guard [%s].',
            $this->guardName(),
        ));

        if (! $prune) {
            $this->components->warn(
                'Orphaned permissions were retained. Use --prune to remove them.',
            );
        }

        return self::SUCCESS;
    }

    private function confirmPruning(): bool
    {
        if ((bool) $this->option('force')) {
            return true;
        }

        if (! $this->input->isInteractive()) {
            $this->components->error(
                'The --prune option requires an interactive terminal or --force.',
            );

            return false;
        }

        return $this->confirm(
            sprintf(
                'Remove permissions for guard [%s] that are no longer discovered?',
                $this->guardName(),
            ),
            false,
        );
    }
}
