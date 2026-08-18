<?php

declare(strict_types=1);

namespace Rimba\Can\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Rimba\Can\Actions\SyncPermissionDefinitions;

#[Description('Synchronize discovered Filament permissions.')]
#[Signature('boleh:sync {--panel=} {--prune}')]
final class SyncPermissionsCommand extends BolehCommand
{
    public function handle(SyncPermissionDefinitions $sync): int
    {
        $names = $sync->handle($this->definitions(), prune: (bool) $this->option('prune'));
        $this->info(count($names).' permission definitions synchronized.');

        return self::SUCCESS;
    }
}
