<?php

declare(strict_types=1);

namespace Rimba\FilamentAuthorization\Filament\Resources\PermissionGroupResource\Pages;

use Filament\Resources\Pages\EditRecord;
use Rimba\FilamentAuthorization\Filament\Resources\PermissionGroupResource;

class EditPermissionGroup extends EditRecord
{
    protected static string $resource = PermissionGroupResource::class;
}
