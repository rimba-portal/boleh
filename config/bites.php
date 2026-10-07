<?php

declare(strict_types=1);

return [
    'dms' => [
        'application' => [
            'path' => base_path('app'),
            'namespace' => 'App\\',
        ],
        'rimba' => [
            'path' => base_path('vendor/rimba'),
        ],
        'discovery' => [
            'resource_crud' => ['view', 'create', 'edit', 'delete'],
            'ignore_namespaces' => ['Rimba\\Can\\Tests\\'],
        ],
        'sync' => [
            'guard' => 'web',
        ],
    ],
];
