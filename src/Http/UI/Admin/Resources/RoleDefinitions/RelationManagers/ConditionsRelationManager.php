<?php

declare(strict_types=1);

namespace Rimba\Can\Http\UI\Admin\Resources\RoleDefinitions\RelationManagers;

use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Rimba\Can\Models\AttributeDefinition;

final class ConditionsRelationManager extends RelationManager
{
    protected static string $relationship = 'conditionGroups';

    protected static ?string $title = 'Conditions';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Condition Group')
                    ->schema([
                        Select::make('parent_id')
                            ->label('Parent Group')
                            ->relationship(
                                name: 'parent',
                                titleAttribute: 'id',
                            )
                            ->getOptionLabelFromRecordUsing(
                                fn ($record): string => sprintf(
                                    'Group #%s (%s)',
                                    $record->getKey(),
                                    strtoupper(
                                        $record->boolean_operator,
                                    ),
                                ),
                            )
                            ->searchable()
                            ->preload()
                            ->nullable(),

                        Select::make('boolean_operator')
                            ->label('Boolean Operator')
                            ->options([
                                'and' => 'AND',
                                'or' => 'OR',
                            ])
                            ->default('and')
                            ->required()
                            ->native(false),

                        TextInput::make('sort')
                            ->numeric()
                            ->default(0)
                            ->required(),
                    ])
                    ->columns(3),

                Section::make('Rules')
                    ->schema([
                        Repeater::make('rules')
                            ->relationship()
                            ->schema([
                                Select::make(
                                    'attribute_definition_id',
                                )
                                    ->label('Attribute')
                                    ->relationship(
                                        name: 'attributeDefinition',
                                        titleAttribute: 'label',
                                    )
                                    ->getOptionLabelFromRecordUsing(
                                        fn (
                                            AttributeDefinition $record,
                                        ): string => sprintf(
                                            '%s (%s)',
                                            $record->label,
                                            $record->code,
                                        ),
                                    )
                                    ->searchable([
                                        'label',
                                        'table_name',
                                        'key',
                                    ])
                                    ->preload()
                                    ->live()
                                    ->afterStateUpdated(
                                        function (
                                            mixed $state,
                                            callable $set,
                                        ): void {
                                            if (! $state) {
                                                return;
                                            }

                                            $attribute =
                                                AttributeDefinition::query()
                                                    ->find($state);

                                            if ($attribute) {
                                                $set(
                                                    'field',
                                                    $attribute->code,
                                                );
                                            }
                                        },
                                    ),

                                TextInput::make('field')
                                    ->placeholder(
                                        'staff.cost_center',
                                    )
                                    ->required()
                                    ->maxLength(255),

                                Select::make('operator')
                                    ->options([
                                        '=' => 'Equals (=)',
                                        '==' => 'Equals (==)',
                                        '!=' => 'Not equal (!=)',
                                        '!==' => 'Strictly not equal (!==)',
                                        '>' => 'Greater than (>)',
                                        '>=' => 'Greater than or equal (>=)',
                                        '<' => 'Less than (<)',
                                        '<=' => 'Less than or equal (<=)',
                                        'in' => 'In',
                                        'not_in' => 'Not in',
                                        'contains' => 'Contains',
                                        'exists' => 'Exists',
                                    ])
                                    ->default('=')
                                    ->required()
                                    ->native(false),

                                Select::make('value_source')
                                    ->label('Value Source')
                                    ->options([
                                        'literal' => 'Literal',
                                        'attribute' => 'Attribute',
                                        'resource' => 'Resource',
                                    ])
                                    ->default('literal')
                                    ->required()
                                    ->native(false),

                                TagsInput::make('value')
                                    ->helperText(
                                        'Enter one value for scalar comparisons or multiple values for in/not-in comparisons.',
                                    )
                                    ->columnSpan(2),

                                TextInput::make('sort')
                                    ->numeric()
                                    ->default(0)
                                    ->required(),

                                Toggle::make('is_active')
                                    ->default(true),
                            ])
                            ->defaultItems(1)
                            ->reorderableWithButtons()
                            ->collapsible()
                            ->itemLabel(
                                fn (array $state): string => filled(
                                    $state['field'] ?? null,
                                )
                                    ? sprintf(
                                        '%s %s',
                                        $state['field'],
                                        $state['operator'] ?? '=',
                                    )
                                    : 'Condition Rule',
                            )
                            ->columns(4)
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->defaultSort('sort')
            ->columns([
                TextColumn::make('id')
                    ->label('Group')
                    ->formatStateUsing(
                        fn (mixed $state): string => 'Group #'.$state,
                    ),

                TextColumn::make('boolean_operator')
                    ->label('Operator')
                    ->badge()
                    ->formatStateUsing(
                        fn (?string $state): string => strtoupper((string) $state),
                    )
                    ->color(
                        fn (?string $state): string => match ($state) {
                            'and' => 'primary',
                            'or' => 'warning',
                            default => 'gray',
                        },
                    ),

                TextColumn::make('parent.id')
                    ->label('Parent')
                    ->formatStateUsing(
                        fn (mixed $state): string => 'Group #'.$state,
                    )
                    ->placeholder('Root'),

                TextColumn::make('rules_count')
                    ->label('Rules')
                    ->counts('rules'),

                TextColumn::make('sort')
                    ->sortable(),

                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('Create Condition Group'),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                DeleteBulkAction::make(),
            ]);
    }
}
