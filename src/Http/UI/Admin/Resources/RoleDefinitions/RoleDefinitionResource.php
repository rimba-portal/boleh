<?php

declare(strict_types=1);

namespace Rimba\Can\Http\UI\Admin\Resources\RoleDefinitions;

use BackedEnum;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
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
use Illuminate\Support\Str;
use Rimba\Can\Enums\RoleType;
use Rimba\Can\Http\UI\Admin\Resources\RoleDefinitions\Pages\CreateRoleDefinition;
use Rimba\Can\Http\UI\Admin\Resources\RoleDefinitions\Pages\EditRoleDefinition;
use Rimba\Can\Http\UI\Admin\Resources\RoleDefinitions\Pages\ListRoleDefinitions;
use Rimba\Can\Http\UI\Admin\Resources\RoleDefinitions\RelationManagers\ConditionsRelationManager;
use Rimba\Can\Http\UI\Admin\Resources\RoleDefinitions\RelationManagers\PermissionsRelationManager;
use Rimba\Can\Models\RoleDefinition;
use UnitEnum;

final class RoleDefinitionResource extends Resource
{
    protected static ?string $model = RoleDefinition::class;

    protected static string|UnitEnum|null $navigationGroup = 'Can';

    protected static string|BackedEnum|null $navigationIcon =
        'heroicon-o-user-group';

    protected static ?int $navigationSort = 20;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Role Definition')
                    ->description(
                        'Define an RBAC, ABAC, hybrid, generated, or system role.',
                    )
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(
                                function (
                                    ?string $state,
                                    callable $set,
                                ): void {
                                    $set(
                                        'slug',
                                        Str::slug((string) $state),
                                    );
                                },
                            ),

                        TextInput::make('slug')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),

                        Select::make('type')
                            ->options(RoleType::options())
                            ->default(RoleType::Rbac->value)
                            ->required()
                            ->native(false)
                            ->live(),

                        TextInput::make('guard_name')
                            ->default('web')
                            ->required()
                            ->maxLength(40),

                        Textarea::make('description')
                            ->rows(3)
                            ->columnSpanFull(),

                        Toggle::make('is_active')
                            ->default(true),

                        Toggle::make('is_generated')
                            ->label('Generated role')
                            ->disabled()
                            ->dehydrated(),
                    ])
                    ->columns(2),

                Section::make('Generation')
                    ->description(
                        'Configure how generated role definitions are produced.',
                    )
                    ->visible(
                        fn (callable $get): bool => $get('type')
                            === RoleType::Generated->value,
                    )
                    ->schema([
                        TextInput::make('generator')
                            ->placeholder(
                                'org_team_job_role',
                            )
                            ->maxLength(255),

                        KeyValue::make('generator_config')
                            ->keyLabel('Setting')
                            ->valueLabel('Value')
                            ->addActionLabel('Add setting')
                            ->columnSpanFull(),
                    ]),

                Section::make('Runtime Role')
                    ->description(
                        'The authorization engine role created from this definition.',
                    )
                    ->schema([
                        Select::make('role_id')
                            ->label('Compiled Runtime Role')
                            ->relationship(
                                name: 'role',
                                titleAttribute: 'name',
                            )
                            ->searchable()
                            ->preload()
                            ->disabled()
                            ->dehydrated(false),
                    ])
                    ->visibleOn('edit'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('name')
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('slug')
                    ->searchable()
                    ->copyable(),

                TextColumn::make('type')
                    ->badge()
                    ->formatStateUsing(
                        fn (
                            RoleType|string|null $state,
                        ): string => $state instanceof RoleType
                            ? $state->label()
                            : strtoupper((string) $state),
                    ),

                TextColumn::make('permissions_count')
                    ->label('Permissions')
                    ->counts('permissions'),

                TextColumn::make('condition_groups_count')
                    ->label('Condition Groups')
                    ->counts('conditionGroups'),

                IconColumn::make('role_id')
                    ->label('Compiled')
                    ->boolean()
                    ->getStateUsing(
                        fn (RoleDefinition $record): bool => $record->isCompiled(),
                    ),

                IconColumn::make('is_generated')
                    ->label('Generated')
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
                SelectFilter::make('type')
                    ->options(RoleType::options()),

                SelectFilter::make('guard_name')
                    ->options(
                        fn (): array => RoleDefinition::query()
                            ->whereNotNull('guard_name')
                            ->distinct()
                            ->orderBy('guard_name')
                            ->pluck('guard_name', 'guard_name')
                            ->all(),
                    ),

                TernaryFilter::make('is_active')
                    ->label('Active'),

                TernaryFilter::make('is_generated')
                    ->label('Generated'),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            PermissionsRelationManager::class,
            ConditionsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListRoleDefinitions::route('/'),
            'create' => CreateRoleDefinition::route('/create'),
            'edit' => EditRoleDefinition::route('/{record}/edit'),
        ];
    }
}
