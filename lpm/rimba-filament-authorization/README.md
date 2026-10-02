# RIMBA Filament Authorization

Filament 5 administration UI for RIMBA authorization, complementing
`hosseinhezami/laravel-permission-manager`.

## Status

Initial scaffold. The Resources intentionally start thin so the UI can be
aligned with the installed version of the underlying permission manager.

## Installation

```bash
composer require rimba/filament-authorization
php artisan rimba:authorization-install
```

Register the plugin in your Filament panel:

```php
use Rimba\FilamentAuthorization\Filament\RimbaAuthorizationPlugin;

->plugins([
    RimbaAuthorizationPlugin::make(),
])
```

The package is designed to present the underlying Role model as a RIMBA
Job Role and later layer RIMBA ABAC concepts around User, Staff and
JobPosition without duplicating the underlying permission engine.
