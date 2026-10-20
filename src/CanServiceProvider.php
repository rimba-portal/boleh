<?php

declare(strict_types=1);

namespace Rimba\Can;

use Illuminate\Console\Command;
use ReflectionClass;
use Rimba\Base\Services\BitesServiceProvider;
use Rimba\Can\Contracts\PermissionSynchronizer;
use Rimba\Can\Observers\OrgTeamRbacObserver;
use Rimba\Can\Observers\OrgUnitRbacObserver;
use Rimba\Can\Services\PermissionDiscoveryService;
use Rimba\Can\Services\PermissionSynchronizerService;
use Rimba\Can\Services\RbacRoleSynchronizer;
use Rimba\Can\Support\RimbaSourceScanner;
use Rimba\Organization\Models\OrgTeam;
use Rimba\Organization\Models\OrgUnit;

class CanServiceProvider extends BitesServiceProvider
{
    protected function bootPackage(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        OrgUnit::observe(OrgUnitRbacObserver::class);
        OrgTeam::observe(OrgTeamRbacObserver::class);

        if ($this->app->runningInConsole()) {
            $this->registerCommandsFromDirectory();
        }
    }

    protected function registerPackage(): void
    {
        $this->app->singleton(RimbaSourceScanner::class);
        $this->app->singleton(PermissionDiscoveryService::class);
        $this->app->singleton(
            PermissionSynchronizer::class,
            PermissionSynchronizerService::class,
        );

        $this->app->singleton(RbacRoleSynchronizer::class);
    }

    /**
     * Dynamically discover and boot all commands inside the package directory.
     */
    protected function registerCommandsFromDirectory()
    {
        $commandDir = __DIR__.'/Console/Commands';
        if (! is_dir($commandDir)) {
            return;
        }

        $commands = [];
        foreach (glob($commandDir.'/*.php') as $file) {
            $className = basename($file, '.php');
            $class = 'Rimba\\Can\\Console\\Commands\\'.$className;
            if (class_exists($class) && is_subclass_of($class, Command::class)) {
                $reflection = new ReflectionClass($class);
                if (! $reflection->isAbstract()) {
                    $commands[] = $class;
                }
            }
        }

        if ($commands !== []) {
            $this->commands($commands);
        }
    }
}
