<?php

declare(strict_types=1);

namespace Rimba\FilamentAuthorization\Filament\Resources;

use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use HosseinHezami\PermissionManager\Models\PermissionSet;
use Rimba\FilamentAuthorization\Filament\Resources\PermissionSetResource\Pages;

class PermissionSetResource extends Resource
{
    protected static ?string $model = PermissionSet::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Access Control';

    protected static ?string $navigationLabel = 'Permission Sets';

    public static function form(Schema $schema): Schema
    {
        return $schema;
    }

    public static function table(Table $table): Table
    {
        return $table;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPermissionSets::route('/'),
            'create' => Pages\CreatePermissionSet::route('/create'),
            'edit' => Pages\EditPermissionSet::route('/{record}/edit'),
        ];
    }
}
