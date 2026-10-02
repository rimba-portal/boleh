# Rimba Boleh initial scaffold

This package is the Filament design layer above `hosseinhezami/laravel-permission-manager`.

## Ownership boundary

- Hossein package owns runtime roles, permissions, assignments, inheritance, permission conditions, audits and authorization logs.
- Rimba Boleh owns attribute metadata, role blueprints, condition-builder data, generators and Filament UI.
- `role_definitions.role_id` links a Rimba blueprint to the compiled runtime role.

## Initial resources

- AttributeDefinitionResource
- RoleDefinitionResource
  - PermissionsRelationManager
  - ConditionsRelationManager

## Next implementation step

Add `CompileRoleDefinition` to synchronize each active definition into the runtime `roles`, `role_permissions`, and `permission_conditions` records. Keep that synchronization explicit rather than model-observer driven.

```text
rimba/boleh
├── composer.json
├── README.md
├── database/
│   └── migrations/
│       └── 0002_01_01_000500_create_boleh_design_tables.php
└── src/
    ├── Enums/
    │   ├── AttributeDataType.php
    │   └── RoleType.php
    ├── Models/
    │   ├── AttributeDefinition.php
    │   ├── ConditionGroup.php
    │   ├── ConditionRule.php
    │   └── RoleDefinition.php
    └── Http/UI/Admin/Resources/
        ├── AttributeDefinitions/
        │   ├── AttributeDefinitionResource.php
        │   └── Pages/
        │       ├── ListAttributeDefinitions.php
        │       ├── CreateAttributeDefinition.php
        │       └── EditAttributeDefinition.php
        └── RoleDefinitions/
            ├── RoleDefinitionResource.php
            ├── Pages/
            │   ├── ListRoleDefinitions.php
            │   ├── CreateRoleDefinition.php
            │   └── EditRoleDefinition.php
            └── RelationManagers/
                ├── PermissionsRelationManager.php
                └── ConditionsRelationManager.php
```