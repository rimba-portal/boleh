<?php

declare(strict_types=1);

namespace Rimba\Can\Http\UI\Admin\Resources\AttributeDefinitions;

use BackedEnum;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Rimba\Can\Enums\AttributeDataType;
use Rimba\Can\Http\UI\Admin\Resources\AttributeDefinitions\Pages\CreateAttributeDefinition;
use Rimba\Can\Http\UI\Admin\Resources\AttributeDefinitions\Pages\EditAttributeDefinition;
use Rimba\Can\Http\UI\Admin\Resources\AttributeDefinitions\Pages\ListAttributeDefinitions;
use Rimba\Can\Models\AttributeDefinition;
use Rimba\People\Models\Staff;
use UnitEnum;

final class AttributeDefinitionResource extends Resource
{
    protected static ?string $model = AttributeDefinition::class;

    protected static string|UnitEnum|null $navigationGroup = 'Can';

    protected static string|BackedEnum|null $navigationIcon =
        'heroicon-o-adjustments-horizontal';

    protected static ?int $navigationSort = 10;

    protected static ?string $recordTitleAttribute = 'label';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Attribute')
                    ->description(
                        'Register an attribute that can be used in ABAC conditions.',
                    )
                    ->schema([
                        TextInput::make('table_name')
                            ->label('Table')
                            ->placeholder('staff')
                            ->helperText(
                                'The subject or aggregate that owns the attribute.',
                            )
                            ->required()
                            ->maxLength(100),

                        TextInput::make('key')
                            ->placeholder('cost_center')
                            ->helperText(
                                'Combined with the table name, such as staff.cost_center.',
                            )
                            ->required()
                            ->maxLength(120),

                        TextInput::make('label')
                            ->placeholder('Cost Center')
                            ->required()
                            ->maxLength(255),

                        Select::make('data_type')
                            ->options(AttributeDataType::options())
                            ->default(AttributeDataType::String->value)
                            ->required()
                            ->native(false),

                        Textarea::make('description')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Section::make('Attribute Source')
                    ->description(
                        'Describe where the attribute value is resolved from.',
                    )
                    ->schema([
                        TextInput::make('source_model')
                            ->placeholder(
                                Staff::class,
                            )
                            ->maxLength(255),

                        TextInput::make('source_relation')
                            ->placeholder('attributes')
                            ->maxLength(255),

                        TextInput::make('source_key_column')
                            ->default('key')
                            ->required()
                            ->maxLength(120),

                        TextInput::make('source_value_column')
                            ->default('value')
                            ->required()
                            ->maxLength(120),
                    ])
                    ->columns(2),

                Section::make('Values')
                    ->schema([
                        TagsInput::make('allowed_values')
                            ->helperText(
                                'Leave empty when values are resolved dynamically.',
                            )
                            ->columnSpanFull(),

                        Toggle::make('is_multiple')
                            ->label('Allow multiple values')
                            ->default(false),

                        Toggle::make('is_active')
                            ->default(true),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('table_name')
            ->columns([
                TextColumn::make('code')
                    ->label('Attribute')
                    ->searchable([
                        'table_name',
                        'key',
                    ])
                    ->sortable(['table_name', 'key'])
                    ->copyable(),

                TextColumn::make('label')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('data_type')
                    ->label('Type')
                    ->badge()
                    ->formatStateUsing(
                        fn (
                            AttributeDataType|string|null $state,
                        ): string => $state instanceof AttributeDataType
                            ? $state->label()
                            : ucfirst((string) $state),
                    ),

                TextColumn::make('source_model')
                    ->label('Source Model')
                    ->placeholder('Not configured')
                    ->toggleable(),

                IconColumn::make('is_multiple')
                    ->label('Multiple')
                    ->boolean(),

                IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean(),

                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('data_type')
                    ->options(AttributeDataType::options()),

                SelectFilter::make('table_name')
                    ->options(
                        fn (): array => AttributeDefinition::query()
                            ->whereNotNull('table_name')
                            ->distinct()
                            ->orderBy('table_name')
                            ->pluck('table_name', 'table_name')
                            ->all(),
                    )
                    ->searchable(),

                TernaryFilter::make('is_active')
                    ->label('Active'),

                TernaryFilter::make('is_multiple')
                    ->label('Multiple values'),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAttributeDefinitions::route('/'),
            'create' => CreateAttributeDefinition::route('/create'),
            'edit' => EditAttributeDefinition::route('/{record}/edit'),
        ];
    }
}
