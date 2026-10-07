# RIMBA Boleh

Composer-ready permission discovery/synchronization for Laravel 13, Filament 5 and Spatie Permission.

Scans `app/` and every `vendor/rimba/*/src` package. See below for operation instructions.

# Boleh Administration Guide

## What it does

Boleh scans both the application and RIMBA packages:

```text
app/
vendor/rimba/*/src/
```

RIMBA package namespaces are read from each package's Composer PSR-4 autoload configuration, so there is no hard-coded package list.

The flow is:

```text
PHP classes -> discovery -> permission definitions -> permissions table
                                              |
                                              +-> JobRole/Spatie Role -> role_has_permissions
```

Boleh does **not** automatically populate `model_has_permissions` or `role_has_permissions`.

## Install

```bash
composer require rimba/boleh
php artisan migrate
```

If config/migrations are not published automatically:

```bash
php artisan vendor:publish --tag=boleh-config
php artisan vendor:publish --tag=boleh-migrations
php artisan migrate
```

## Commands

Preview source-derived permissions:

```bash
php artisan boleh:scan
```

Check source/database differences:

```bash
php artisan boleh:check
```

Synchronize the permission catalogue:

```bash
php artisan boleh:sync
```

Synchronize and remove permissions no longer discovered:

```bash
php artisan boleh:sync --prune
```

## Recommended operation

Run synchronization after installation and deployments, not on every request and normally not on a scheduler.

Example deployment:

```bash
composer install --no-interaction --prefer-dist
php artisan migrate --force
php artisan boleh:check
php artisan boleh:sync
php artisan optimize
```

Use `--prune` only after reviewing obsolete permissions.

## Permission sources

### Filament Resource

A Resource produces CRUD permissions:

```text
<package>.<resource>.view
<package>.<resource>.create
<package>.<resource>.edit
<package>.<resource>.delete
```

### Filament Page

A Page produces:

```text
<package>.<page>.view
```

### Explicit business action

Use the attribute for business capabilities:

```php
use Rimba\Can\Attributes\Permission;

#[Permission(
    name: 'dms.document.approve',
    type: 'action',
    description: 'Approve a controlled document.'
)]
final class ApproveDocument {}
```

This permission is a business capability and can be used from Filament, API, jobs, console, workflow, or automation.

### Explicit resource name

```php
use Rimba\Can\Attributes\PermissionResource;

#[PermissionResource(name: 'dms.document')]
class DocumentResource extends Resource {}
```

## RBAC and ABAC

RBAC answers:

```text
Does this JobRole have dms.document.approve?
```

ABAC answers whether that subject may perform the capability on the particular record/context.

Therefore discovery creates the capability catalogue; it does not replace role assignment or ABAC rules.

## Troubleshooting

If a class is not discovered:

```bash
composer dump-autoload
php artisan boleh:scan
```

For a RIMBA package, check that its `composer.json` has a PSR-4 mapping such as:

```json
"autoload": {
  "psr-4": {
    "Rimba\\Dms\\": "src/"
  }
}
```

Then run:

```bash
composer dump-autoload
```
