<?php

declare(strict_types=1);

return [
    'can' => [
        'application' => [
            'path' => base_path('app'),
            'namespace' => 'App\\',
        ],

        'rimba' => [
            /*
             * Your current packages are stored under:
             *
             * base_path('rimba')
             *
             * Change this to base_path('vendor/rimba') only after the
             * packages are installed as normal Composer vendor packages.
             */
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
