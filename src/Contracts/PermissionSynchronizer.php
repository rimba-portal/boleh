<?php

declare(strict_types=1);

namespace Rimba\Can\Contracts;

interface PermissionSynchronizer
{
    public function sync(array $definitions, bool $prune = false): array;
}
