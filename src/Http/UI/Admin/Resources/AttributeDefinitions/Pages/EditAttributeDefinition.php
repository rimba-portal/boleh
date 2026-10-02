<?php

declare(strict_types=1);

namespace Rimba\Can\Http\UI\Admin\Resources\AttributeDefinitions\Pages;

use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Rimba\Can\Http\UI\Admin\Resources\AttributeDefinitions\AttributeDefinitionResource;

final class EditAttributeDefinition extends EditRecord
{
    protected static string $resource =
        AttributeDefinitionResource::class;

    protected static ?string $title = 'Edit Attribute Definition';

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
