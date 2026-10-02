<?php

declare(strict_types=1);

namespace Rimba\FilamentAuthorization\Filament\Resources\JobRoleResource\Pages;

use Filament\Resources\Pages\ListRecords;
use Rimba\FilamentAuthorization\Filament\Resources\JobRoleResource;

class ListJobRoles extends ListRecords
{
    protected static string $resource = JobRoleResource::class;
}
