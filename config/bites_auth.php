<?php

declare(strict_types=1);

use Rimba\Can\Services\DefaultAttributeResolver;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

return [
    'guard' => 'web',

    'models' => [
        'role' => Role::class,
        'permission' => Permission::class,
    ],

    'attributes' => [
        'resolver' => DefaultAttributeResolver::class,
        'json_column' => 'attributes',
        'relation' => 'attributes',
        'key_column' => 'key',
        'value_column' => 'value',
    ],

    'discovery' => [
        'panels' => true,
        'resources' => true,
        'pages' => true,
        'widgets' => false,
        'resource_permissions' => [
            'view_any', 'view', 'create', 'update', 'delete', 'delete_any',
            'restore', 'restore_any', 'force_delete', 'force_delete_any',
            'replicate', 'reorder',
        ],
    ],

    'panels' => [
        'priority' => ['admin', 'team', 'staff-sensitive', 'staff', 'lobby'],
        'fallback' => 'lobby',
        'permission_pattern' => 'access_%s_panel',
    ],

    'sync' => [
        'prune_generated_permissions' => false,
    ],
];
