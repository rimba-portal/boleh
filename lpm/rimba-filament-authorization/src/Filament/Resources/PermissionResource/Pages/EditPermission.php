<?php

declare(strict_types=1);

namespace Rimba\FilamentAuthorization\Filament\Resources\PermissionResource\Pages;

use Filament\Resources\Pages\EditRecord;
use Rimba\FilamentAuthorization\Filament\Resources\PermissionResource;

class EditPermission extends EditRecord
{
    protected static string $resource = PermissionResource::class;
}
