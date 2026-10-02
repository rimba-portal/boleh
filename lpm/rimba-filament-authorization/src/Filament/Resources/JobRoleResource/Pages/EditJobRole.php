<?php

declare(strict_types=1);

namespace Rimba\FilamentAuthorization\Filament\Resources\JobRoleResource\Pages;

use Filament\Resources\Pages\EditRecord;
use Rimba\FilamentAuthorization\Filament\Resources\JobRoleResource;

class EditJobRole extends EditRecord
{
    protected static string $resource = JobRoleResource::class;
}
