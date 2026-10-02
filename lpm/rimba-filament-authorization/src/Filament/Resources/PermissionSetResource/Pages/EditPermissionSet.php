<?php

declare(strict_types=1);

namespace Rimba\FilamentAuthorization\Filament\Resources\PermissionSetResource\Pages;

use Filament\Resources\Pages\EditRecord;
use Rimba\FilamentAuthorization\Filament\Resources\PermissionSetResource;

class EditPermissionSet extends EditRecord
{
    protected static string $resource = PermissionSetResource::class;
}
