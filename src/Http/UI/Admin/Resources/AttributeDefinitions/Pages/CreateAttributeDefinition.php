<?php

declare(strict_types=1);

namespace Rimba\Can\Http\UI\Admin\Resources\AttributeDefinitions\Pages;

use Filament\Resources\Pages\CreateRecord;
use Rimba\Can\Http\UI\Admin\Resources\AttributeDefinitions\AttributeDefinitionResource;

final class CreateAttributeDefinition extends CreateRecord
{
    protected static string $resource =
        AttributeDefinitionResource::class;

    protected static ?string $title = 'Create Attribute Definition';
}
