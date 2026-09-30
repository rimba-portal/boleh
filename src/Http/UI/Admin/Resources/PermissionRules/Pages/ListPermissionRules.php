<?php

declare(strict_types=1);

namespace Rimba\Can\Http\UI\Admin\Resources\PermissionRules\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Rimba\Can\Http\UI\Admin\Resources\PermissionRules\PermissionRuleResource;

class ListPermissionRules extends ListRecords
{
    protected static string $resource = PermissionRuleResource::class;

    protected static ?string $title = 'Access Permission Rules';

    protected ?string $subheading = 'Define fine-grained access rules and operations using operator filters.';

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
