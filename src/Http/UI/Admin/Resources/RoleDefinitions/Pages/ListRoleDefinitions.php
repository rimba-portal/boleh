<?php

declare(strict_types=1);

namespace Rimba\Can\Http\UI\Admin\Resources\RoleDefinitions\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Rimba\Can\Http\UI\Admin\Resources\RoleDefinitions\RoleDefinitionResource;

final class ListRoleDefinitions extends ListRecords
{
    protected static string $resource =
        RoleDefinitionResource::class;

    protected static ?string $title = 'Role Definitions';

    protected ?string $subheading =
        'Design RBAC, ABAC, hybrid, generated, and system roles before compiling them into runtime authorization records.';

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Create Role'),
        ];
    }
}
