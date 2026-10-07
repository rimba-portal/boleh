<?php

declare(strict_types=1);

namespace Rimba\Can\Data;

final readonly class PermissionDefinition
{
    public function __construct(public string $name, public string $type, public ?string $package = null, public ?string $resource = null, public ?string $action = null, public ?string $class = null, public ?string $description = null) {}
}
