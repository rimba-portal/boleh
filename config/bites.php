<?php

declare(strict_types=1);

return [
    'can' => [
        'application' => [
            'path' => base_path('app'),
            'namespace' => 'App\\',
        ],

        'rimba' => [
            'path' => base_path('vendor/rimba'),
        ],

        'discovery' => [
            'resource_crud' => [
                'viewAny',
                'view',
                'create',
                'update',
                'delete',
                'restore',
                'forceDelete',
            ],

            'ignore_namespaces' => [
                'Rimba\\Can\\Tests\\',
            ],
        ],

        'sync' => [
            'guard' => 'web',
        ],

        'rbac' => [
            'org_unit_roles' => [
                'owner',
                'member',
            ],

            'org_team_roles' => [
                'captain',
                'scout',
                'player',
                'quartermaster',
                'tactician',
                'coach',
            ],

            'global_roles' => [
                // Exceptional non-package global roles may be added here later.
            ],

            'package_managers' => [
                'dms',
                'lms',
                'wfm',
                'work',
                'time',
                'floorplan',
            ],
        ],
    ],
];
