<?php

declare(strict_types=1);
use App\Models\JobPosition;
use App\Models\Staff;
use App\Models\User;
use HosseinHezami\PermissionManager\Models\Permission;
use HosseinHezami\PermissionManager\Models\PermissionGroup;
use HosseinHezami\PermissionManager\Models\PermissionSet;
use HosseinHezami\PermissionManager\Models\Role;

return [
    'navigation' => [
        'group' => 'Access Control',
        'icon' => 'heroicon-o-shield-check',
        'sort' => 1,
    ],

    'resources' => [
        'job_roles' => true,
        'permissions' => true,
        'permission_groups' => true,
        'permission_sets' => true,
    ],

    'models' => [
        'user' => env('RIMBA_AUTHORIZATION_USER_MODEL', User::class),
        'staff' => env('RIMBA_AUTHORIZATION_STAFF_MODEL', Staff::class),
        'job_position' => env('RIMBA_AUTHORIZATION_JOB_POSITION_MODEL', JobPosition::class),
        'job_role' => env(
            'RIMBA_AUTHORIZATION_JOB_ROLE_MODEL',
            Role::class
        ),
    ],

    'permission_manager' => [
        'role_model' => Role::class,
        'permission_model' => Permission::class,
        'permission_group_model' => PermissionGroup::class,
        'permission_set_model' => PermissionSet::class,
    ],

    'publish_migrations' => true,
];
