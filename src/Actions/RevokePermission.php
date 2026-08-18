<?php

declare(strict_types=1);

namespace Rimba\Can\Actions;

use Illuminate\Contracts\Auth\Authenticatable;
use Spatie\Permission\PermissionRegistrar;

final class RevokePermission
{
    public function handle(Authenticatable $subject, string $permission): void
    {
        $subject->revokePermissionTo($permission);
        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
