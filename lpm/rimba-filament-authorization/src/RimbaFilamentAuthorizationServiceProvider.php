<?php

declare(strict_types=1);

namespace Rimba\FilamentAuthorization;

use Illuminate\Support\ServiceProvider;

class RimbaFilamentAuthorizationServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/rimba-authorization.php',
            'rimba-authorization',
        );
    }

    public function boot(): void
    {
        $this->publishes([
            __DIR__.'/../config/rimba-authorization.php' => config_path('rimba-authorization.php'),
        ], 'rimba-authorization-config');

        $this->loadViewsFrom(
            __DIR__.'/../resources/views',
            'rimba-authorization',
        );

        if (config('rimba-authorization.publish_migrations', true)) {
            $this->loadMigrationsFrom(
                __DIR__.'/../database/migrations',
            );
        }

        if ($this->app->runningInConsole()) {
            $this->commands([
                Commands\InstallCommand::class,
            ]);
        }
    }
}
