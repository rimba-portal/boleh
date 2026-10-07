<?php

declare(strict_types=1);

namespace Rimba\Can\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
final readonly class PermissionResource
{
    public function __construct(
        public string $name,
        public ?string $description = null
    ) {}
}
