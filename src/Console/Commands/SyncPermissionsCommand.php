<?php

declare(strict_types=1);

namespace Rimba\Can\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Rimba\Can\Contracts\PermissionSynchronizer;

#[Description('Synchronize discovered permissions into Spatie permissions.')]
#[Signature('rimba:lock-sync {--prune : Remove permissions no longer discovered}')]
final class SyncPermissionsCommand extends BolehCommand
{
    public function handle(PermissionSynchronizer $sync): int
    {
        $r = $sync->sync($this->definitions(), (bool) $this->option('prune'));
        $this->table(
            ['Metric', 'Count'],
            [
                ['Discovered', $r['total']],
                ['Created', $r['created']],
                ['Updated', $r['updated']],
                ['Deleted', $r['deleted']],
            ]
        );
        $this->info('Permission synchronization complete.');

        return self::SUCCESS;
    }
}
