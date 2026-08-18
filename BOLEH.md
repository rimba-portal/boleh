# Rimba Boleh

## Purpose

Rimba Boleh is the authorization package for the Rimba ecosystem. Rimba Siapa authenticates and identifies the actor. Rimba Boleh decides what that authenticated actor may access or perform.

## Design Principle

```text
Siapa -> authentication, identity, roles, role assignment
Sifat -> attribute definitions and attribute values
Boleh -> permissions, policies, panel access, RBAC and ABAC evaluation
```

Boleh consumes roles from Siapa and attributes from Sifat. It does not duplicate either source of truth.

## Core Dependencies

- `rimba/asas`
- `rimba/siapa`
- `rimba/sifat`
- `spatie/laravel-permission`
- Filament 5

## Source of Truth

### Siapa

- `roles`
- `model_has_roles`
- role assignment to Staff or another configured authorization subject

### Boleh

- `permissions`
- `role_has_permissions`
- `model_has_permissions`
- `permission_rules`

### Sifat

- attribute definitions
- attribute values attached to the authorization subject

## Permission Schema

The `permissions`, `role_has_permissions`, and `model_has_permissions` tables follow the standard Spatie Laravel Permission schema. Boleh does not add category, description, or type columns to `permissions`.

## Authorization Model

A decision is allowed when:

1. The subject receives the permission through a Siapa role or direct Spatie permission.
2. Every applicable required ABAC rule passes.
3. At least one applicable alternative rule group passes when grouped rules are present.

A permission without ABAC rules is decided through RBAC only.

## ABAC Rules

Rules are stored separately in `permission_rules` and reference a standard Spatie permission.

Each rule contains:

- `attribute_key`
- `operator`
- `value` as JSON
- `group`
- `required`
- `is_active`

Supported operators in the first implementation:

```text
equals
not_equals
in
not_in
contains
exists
not_exists
greater_than
greater_than_or_equal
less_than
less_than_or_equal
```

Boleh retrieves attributes through `AttributeResolverContract`. The default resolver reads common attribute relations or JSON attributes without making Boleh own Sifat data.

## Filament Permission Discovery

Boleh scans registered Filament panels.

### Panel

```text
access_{panel}_panel
```

### Resource

```text
view_any_{resource}
view_{resource}
create_{resource}
update_{resource}
delete_{resource}
delete_any_{resource}
restore_{resource}
restore_any_{resource}
force_delete_{resource}
force_delete_any_{resource}
replicate_{resource}
reorder_{resource}
```

### Page

```text
view_{page}
```

### Widget

Widget discovery is supported and can be enabled from configuration:

```text
view_{widget}_widget
```

Discovery creates normalized permission definitions. Generation inserts missing records. Synchronization can optionally remove generated permissions that no longer exist, but destructive cleanup is disabled by default.

## Panel Access

Panel access belongs to Boleh.

Default panel permissions:

```text
access_lobby_panel
access_staff_panel
access_staff_sensitive_panel
access_team_panel
access_admin_panel
```

`PanelAccessService` resolves accessible panels and the preferred destination panel from configured priority. Siapa's login response may consume `PanelAccessResolverContract` from Boleh.

## Main Workflows

### Discover

```text
Filament panels -> resources/pages/widgets -> permission definitions
```

### Generate

```text
definitions -> create missing Spatie permissions
```

### Synchronize

```text
discovered definitions + configured permissions -> permissions table
```

### Authorize

```text
authenticated subject -> RBAC permission -> ABAC rules -> decision
```

## Commands

```bash
php artisan boleh:scan
php artisan boleh:generate
php artisan boleh:sync
```

- `boleh:scan` previews discovered definitions as JSON.
- `boleh:generate` creates missing permissions.
- `boleh:sync` creates missing permissions and supports guarded cleanup with `--prune`.

All commands support optional `--panel=` filtering.

## Admin Experience

The compact package includes a permission administration resource segment. Production classes should later be split into individual PSR-4 files. Role management and role assignment remain in Siapa; Boleh's permission form attaches permissions to those existing roles.

## Audit Integration

Boleh emits domain events for permission grants, revocations, synchronization, and authorization decisions. Rimba Jejak may listen to these events without Boleh depending directly on Jejak.

## Recommended Package Structure

```text
composer.json
BOLEH.md
README.md
FILES.txt
config/boleh.php
database/migrations/create_boleh_tables.php
database/seeders/BolehSeeder.php
resources/data/boleh/permissions.json
src/BolehServiceProvider.php
src/Contracts/BolehContracts.php
src/Enums/BolehEnums.php
src/Models/BolehModels.php
src/Traits/BolehTraits.php
src/Actions/BolehActions.php
src/Services/BolehServices.php
src/Policies/BolehPolicies.php
src/Console/Commands/BolehCommands.php
src/Http/Middleware/BolehMiddleware.php
src/Http/UI/Admin/Resources/BolehResources.php
```

## Final Boundary

```text
Siapa owns roles.
Boleh assigns permissions to those roles.
Sifat owns attributes.
Boleh evaluates those attributes.
Boleh owns panel access and all authorization decisions.
```
