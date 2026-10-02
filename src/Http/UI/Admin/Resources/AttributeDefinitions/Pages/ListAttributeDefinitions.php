<?php

declare(strict_types=1);

namespace Rimba\Can\Http\UI\Admin\Resources\AttributeDefinitions\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Rimba\Can\Http\UI\Admin\Resources\AttributeDefinitions\AttributeDefinitionResource;

final class ListAttributeDefinitions extends ListRecords
{
    protected static string $resource =
        AttributeDefinitionResource::class;

    protected static ?string $title = 'Attribute Definitions';

    protected ?string $subheading =
        'Register the user, staff, position, organization, and resource attributes available to ABAC conditions.';

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Create Attribute'),
        ];
    }
}
