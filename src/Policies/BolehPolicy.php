<?php

declare(strict_types=1);

namespace Rimba\Can\Policies;

use Illuminate\Contracts\Auth\Authenticatable;
use Rimba\Can\Contracts\AuthorizationServiceContract;

abstract class BolehPolicy
{
    public function __construct(protected AuthorizationServiceContract $authorization) {}

    protected function allows(Authenticatable $subject, string $ability): bool
    {
        return $this->authorization->allows($subject, $ability);
    }
}
