<?php

declare(strict_types=1);

namespace Rimba\Can\Contracts;

use Illuminate\Contracts\Auth\Authenticatable;

interface AttributeResolverContract
{
    /** @return array<string, mixed> */
    public function resolve(Authenticatable $subject): array;
}
