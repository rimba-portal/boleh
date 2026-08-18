<?php

declare(strict_types=1);

namespace Rimba\Can\Console\Commands;

use Illuminate\Console\Command;
use Rimba\Can\Services\PermissionDiscoveryService;

abstract class BolehCommand extends Command
{
    protected function definitions(): array
    {
        return app(PermissionDiscoveryService::class)->discover($this->option('panel'));
    }
}
