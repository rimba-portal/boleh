<?php

declare(strict_types=1);

namespace Rimba\FilamentAuthorization\Filament\Resources\PermissionGroupResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Rimba\FilamentAuthorization\Filament\Resources\PermissionGroupResource;

class CreatePermissionGroup extends CreateRecord
{
    protected static string $resource = PermissionGroupResource::class;
}
