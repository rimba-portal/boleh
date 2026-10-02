<?php

declare(strict_types=1);

namespace Rimba\FilamentAuthorization\Filament\Resources\PermissionSetResource\Pages;

use Filament\Resources\Pages\ListRecords;
use Rimba\FilamentAuthorization\Filament\Resources\PermissionSetResource;

class ListPermissionSets extends ListRecords
{
    protected static string $resource = PermissionSetResource::class;
}
