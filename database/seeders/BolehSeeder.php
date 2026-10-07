<?php

declare(strict_types=1);

namespace Rimba\Can\Database\Seeders;

use Illuminate\Database\Seeder;
use Rimba\Can\Contracts\PermissionSynchronizer;
use Rimba\Can\Services\PermissionDiscoveryService;

final class BolehSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionSynchronizer::class)->sync(app(PermissionDiscoveryService::class)->discover());
    }
}
