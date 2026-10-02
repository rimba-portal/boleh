<?php

declare(strict_types=1);

namespace Rimba\Can\Http\UI\Admin\Resources\RoleDefinitions\RelationManagers;

use Filament\Actions\AttachAction;
use Filament\Actions\DetachAction;
use Filament\Actions\DetachBulkAction;
use Filament\Forms\Components\Select;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

final class PermissionsRelationManager extends RelationManager
{
    protected static string $relationship = 'permissions';

    protected static ?string $title = 'Permissions';

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('route')
            ->columns([
                TextColumn::make('route')
                    ->label('Permission')
                    ->searchable()
                    ->sortable()
                    ->copyable(),

                TextColumn::make('pivot.effect')
                    ->label('Effect')
                    ->badge()
                    ->color(
                        fn (?string $state): string => match ($state) {
                            'allow' => 'success',
                            'deny' => 'danger',
                            default => 'gray',
                        },
                    ),

                TextColumn::make('description')
                    ->placeholder('No description')
                    ->toggleable(),

                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->headerActions([
                AttachAction::make()
                    ->label('Attach Permission')
                    ->preloadRecordSelect()
                    ->pivotData([
                        'effect' => 'allow',
                    ])
                    ->form(
                        fn (AttachAction $action): array => [
                            $action->getRecordSelect(),

                            Select::make('effect')
                                ->options([
                                    'allow' => 'Allow',
                                    'deny' => 'Deny',
                                ])
                                ->default('allow')
                                ->required()
                                ->native(false),
                        ],
                    ),
            ])
            ->recordActions([
                DetachAction::make(),
            ])
            ->toolbarActions([
                DetachBulkAction::make(),
            ]);
    }
}
