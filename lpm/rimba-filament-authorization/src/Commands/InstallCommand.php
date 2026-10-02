<?php

declare(strict_types=1);

namespace Rimba\FilamentAuthorization\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Description('Install RIMBA Filament Authorization')]
#[Signature('rimba:authorization-install
        {--force : Overwrite published configuration files}')]
class InstallCommand extends Command
{
    public function handle(): int
    {
        $this->call('vendor:publish', [
            '--tag' => 'rimba-authorization-config',
            '--force' => $this->option('force'),
        ]);

        $this->info('RIMBA Filament Authorization installed.');

        return self::SUCCESS;
    }
}
