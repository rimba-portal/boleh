<?php

declare(strict_types=1);

namespace Rimba\Can\Traits;

use Rimba\Can\Contracts\AuthorizationServiceContract;

trait IsAuthorized
{
    public function can(string $permission): bool
    {
        return app(AuthorizationServiceContract::class)->allows($this, $permission);
    }

    public function cannot(string $permission): bool
    {
        return ! $this->can($permission);
    }
}
