<?php

declare(strict_types=1);

namespace Rimba\Can\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Rimba\Can\Actions\SyncPermissionDefinitions;

final class BolehSeeder extends Seeder
{
    public function run(): void
    {
        $path = dirname(__DIR__, 2).'/resources/data/boleh/permissions.json';
        if (! File::exists($path)) {
            return;
        }

        $definitions = json_decode(File::get($path), true, 512, JSON_THROW_ON_ERROR);
        app(SyncPermissionDefinitions::class)->handle($definitions, prune: false);
    }
}
