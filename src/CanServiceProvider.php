<?php

declare(strict_types=1);

namespace Rimba\Can;

use Illuminate\Console\Command;
use ReflectionClass;
use Rimba\Base\Services\BitesServiceProvider;
use Rimba\Can\Contracts\AttributeResolverContract;
use Rimba\Can\Contracts\AuthorizationServiceContract;
use Rimba\Can\Contracts\PanelAccessResolverContract;
use Rimba\Can\Services\AuthorizationService;
use Rimba\Can\Services\PanelAccessService;

class CanServiceProvider extends BitesServiceProvider
{
    protected string $configFile = __DIR__.'/../config/bites.php';

    protected function bootPackage(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        if ($this->app->runningInConsole()) {
            $this->registerCommandsFromDirectory();
        }

    }

    protected function registerPackage(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/bites_auth.php', 'bites_auth');
        $this->app->bind(AttributeResolverContract::class, config('bites_auth.attributes.resolver'));
        $this->app->singleton(AuthorizationServiceContract::class, AuthorizationService::class);
        $this->app->singleton(PanelAccessResolverContract::class, PanelAccessService::class);

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
