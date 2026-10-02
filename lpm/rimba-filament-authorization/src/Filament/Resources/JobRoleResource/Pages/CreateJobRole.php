<?php

declare(strict_types=1);

namespace Rimba\FilamentAuthorization\Filament\Resources\JobRoleResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Rimba\FilamentAuthorization\Filament\Resources\JobRoleResource;

class CreateJobRole extends CreateRecord
{
    protected static string $resource = JobRoleResource::class;
}
