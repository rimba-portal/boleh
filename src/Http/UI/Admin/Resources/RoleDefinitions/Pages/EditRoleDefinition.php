<?php

declare(strict_types=1);

namespace Rimba\Can\Http\UI\Admin\Resources\RoleDefinitions\Pages;

use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Rimba\Can\Http\UI\Admin\Resources\RoleDefinitions\RoleDefinitionResource;

final class EditRoleDefinition extends EditRecord
{
    protected static string $resource =
        RoleDefinitionResource::class;

    protected static ?string $title = 'Edit Role Definition';

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->disabled(
                    fn (): bool => $this->record->isCompiled(),
                )
                ->helperText(
                    'A compiled role definition must be unpublished before it can be deleted.',
                ),
        ];
    }
}
