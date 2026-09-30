<?php

declare(strict_types=1);

namespace Rimba\Can\Http\UI\Admin\Resources\PermissionRules;

use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Rimba\Can\Http\UI\Admin\Resources\PermissionRules\Pages\ListPermissionRules;
use Rimba\Can\Models\PermissionRule;
use UnitEnum;

class PermissionRuleResource extends Resource
{
    protected static ?string $model = PermissionRule::class;

    protected static string|UnitEnum|null $navigationGroup = 'Can';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-play';

    protected static ?int $navigationSort = 45;

    protected static ?string $recordTitleAttribute = 'id';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPermissionRules::route('/'),
            // 'create' => \Rimba\Can\Http\UI\Admin\Resources\PermissionRules\Pages\CreatePermissionRule::route('/create'),
            // 'view' => \Rimba\Can\Http\UI\Admin\Resources\PermissionRules\Pages\ViewPermissionRule::route('/{record}'),
            // 'edit' => \Rimba\Can\Http\UI\Admin\Resources\PermissionRules\Pages\EditPermissionRule::route('/{record}/edit'),
            //
        ];
    }
}
