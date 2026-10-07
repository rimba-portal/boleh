<?php

declare(strict_types=1);

namespace Rimba\Can\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
final readonly class Permission
{
    public function __construct(public string $name, public string $type = 'action', public ?string $description = null) {}
}
