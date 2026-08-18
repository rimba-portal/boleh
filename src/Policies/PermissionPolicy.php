<?php

declare(strict_types=1);

namespace Rimba\Can\Policies;

use Illuminate\Contracts\Auth\Authenticatable;

final class PermissionPolicy extends BolehPolicy
{
    public function viewAny(Authenticatable $subject): bool
    {
        return $this->allows($subject, 'manage_permissions');
    }

    public function view(Authenticatable $subject): bool
    {
        return $this->allows($subject, 'manage_permissions');
    }

    public function create(Authenticatable $subject): bool
    {
        return $this->allows($subject, 'manage_permissions');
    }

    public function update(Authenticatable $subject): bool
    {
        return $this->allows($subject, 'manage_permissions');
    }

    public function delete(Authenticatable $subject): bool
    {
        return $this->allows($subject, 'manage_permissions');
    }
}
