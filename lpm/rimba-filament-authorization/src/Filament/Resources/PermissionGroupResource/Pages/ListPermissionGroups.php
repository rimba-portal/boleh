<?php

declare(strict_types=1);

namespace Rimba\FilamentAuthorization\Filament\Resources\PermissionGroupResource\Pages;

use Filament\Resources\Pages\ListRecords;
use Rimba\FilamentAuthorization\Filament\Resources\PermissionGroupResource;

class ListPermissionGroups extends ListRecords
{
    protected static string $resource = PermissionGroupResource::class;
}
