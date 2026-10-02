<?php

declare(strict_types=1);

namespace Rimba\FilamentAuthorization\Filament\Resources\PermissionSetResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Rimba\FilamentAuthorization\Filament\Resources\PermissionSetResource;

class CreatePermissionSet extends CreateRecord
{
    protected static string $resource = PermissionSetResource::class;
}
