<?php

declare(strict_types=1);

namespace Rimba\FilamentAuthorization\Filament\Resources\PermissionResource\Pages;

use Filament\Resources\Pages\ListRecords;
use Rimba\FilamentAuthorization\Filament\Resources\PermissionResource;

class ListPermissions extends ListRecords
{
    protected static string $resource = PermissionResource::class;
}
