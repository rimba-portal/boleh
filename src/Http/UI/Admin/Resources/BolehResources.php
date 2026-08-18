<?php

declare(strict_types=1);

namespace Rimba\Can\Http\UI\Admin\Resources;

/*
 | Compact planning segment.
 |
 | Split into production PSR-4 classes before registering with Filament:
 |
 | Permissions/PermissionResource.php
 | Permissions/Pages/ListPermissions.php
 | Permissions/Pages/CreatePermission.php
 | Permissions/Pages/EditPermission.php
 | Permissions/Schemas/PermissionForm.php
 | Permissions/Tables/PermissionsTable.php
 |
 | The resource should use config('bites_auth.models.permission'), expose name and
 | guard_name, attach existing Siapa roles through the Spatie permissions
 | relationship, and manage PermissionRule records in a relation manager.
 */
