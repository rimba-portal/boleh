# rimba/boleh

Authorization for the Rimba ecosystem using standard Spatie permissions, Siapa roles, Sifat attributes, Laravel policies, Filament discovery, RBAC, and ABAC.

## Install

```bash
composer require rimba/boleh
php artisan migrate
php artisan boleh:generate
```

## Commands

```bash
php artisan boleh:scan
php artisan boleh:generate
php artisan boleh:sync
```

## Package ownership

- Siapa owns roles and role assignment.
- Sifat owns attributes.
- Boleh owns permissions, ABAC rules, policies, permission discovery, and panel access.

See `BOLEH.md` for the full design.
