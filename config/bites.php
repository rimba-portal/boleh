<?php

declare(strict_types=1);

return [
    'can' => [
        'application' => [
            'path' => base_path('app'),
            'namespace' => 'App\\',
        ],

        'rimba' => [
            'path' => base_path('rimba'),
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
    ],
];
