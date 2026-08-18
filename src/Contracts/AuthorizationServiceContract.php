<?php

declare(strict_types=1);

namespace Rimba\Can\Contracts;

use Illuminate\Contracts\Auth\Authenticatable;

interface AuthorizationServiceContract
{
    public function allows(Authenticatable $subject, string $permission): bool;

    public function denies(Authenticatable $subject, string $permission): bool;
}
