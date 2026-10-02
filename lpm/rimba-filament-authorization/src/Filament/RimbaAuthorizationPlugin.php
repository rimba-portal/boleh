<?php

declare(strict_types=1);

namespace Rimba\FilamentAuthorization\Filament;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Rimba\FilamentAuthorization\Filament\Pages\AccessControlDashboard;
use Rimba\FilamentAuthorization\Filament\Resources\JobRoleResource;
use Rimba\FilamentAuthorization\Filament\Resources\PermissionGroupResource;
use Rimba\FilamentAuthorization\Filament\Resources\PermissionResource;
use Rimba\FilamentAuthorization\Filament\Resources\PermissionSetResource;

class RimbaAuthorizationPlugin implements Plugin
{
    public static function make(): static
    {
        return new static;
    }

    public function getId(): string
    {
        return 'rimba-authorization';
    }

    public function register(Panel $panel): void
    {
        $panel
            ->resources([
                JobRoleResource::class,
                PermissionResource::class,
                PermissionGroupResource::class,
                PermissionSetResource::class,
            ])
            ->pages([
                AccessControlDashboard::class,
            ]);
    }

    public function boot(Panel $panel): void
    {
        //
    }
}
