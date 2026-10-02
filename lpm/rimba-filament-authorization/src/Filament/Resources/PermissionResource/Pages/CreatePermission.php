<?php

declare(strict_types=1);

namespace Rimba\FilamentAuthorization\Filament\Resources\PermissionResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Rimba\FilamentAuthorization\Filament\Resources\PermissionResource;

class CreatePermission extends CreateRecord
{
    protected static string $resource = PermissionResource::class;
}
