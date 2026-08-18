<?php

declare(strict_types=1);

namespace Rimba\Can\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Rimba\Can\Actions\SyncPermissionDefinitions;

#[Description('Create missing permissions discovered from Filament.')]
#[Signature('boleh:generate {--panel=}')]
final class GeneratePermissionsCommand extends BolehCommand
{
    public function handle(SyncPermissionDefinitions $sync): int
    {
        $names = $sync->handle($this->definitions(), prune: false);
        $this->info(count($names).' permission definitions generated.');

        return self::SUCCESS;
    }
}
