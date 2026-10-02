<?php

declare(strict_types=1);

namespace Rimba\Can\Http\UI\Admin\Resources\RoleDefinitions\Pages;

use Filament\Resources\Pages\CreateRecord;
use Rimba\Can\Http\UI\Admin\Resources\RoleDefinitions\RoleDefinitionResource;

final class CreateRoleDefinition extends CreateRecord
{
    protected static string $resource =
        RoleDefinitionResource::class;

    protected static ?string $title = 'Create Role Definition';
}
