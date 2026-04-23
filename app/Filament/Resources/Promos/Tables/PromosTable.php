<?php

namespace App\Filament\Resources\Promos\Tables;

use App\Enums\PromoStatus;
use App\Models\Promo;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class PromosTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label(__('filament.promos.table_title'))
                    ->state(fn (Promo $record): string => $record->getTranslation('title') ?? '')
                    ->searchable()
                    ->sortable(),
                ImageColumn::make('image_url')
                    ->label(__('filament.promos.table_image'))
                    ->circular()
                    ->disk('public'),
                TextColumn::make('status')
                    ->label(__('filament.promos.table_status'))
                    ->badge()
                    ->sortable(),
                TextColumn::make('starts_at')
                    ->label(__('filament.promos.table_start'))
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
                TextColumn::make('ends_at')
                    ->label(__('filament.promos.table_end'))
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
                TextColumn::make('priority')
                    ->label(__('filament.promos.table_priority'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('creator.name')
                    ->label(__('filament.promos.table_creator'))
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('created_at')
                    ->label(__('filament.promos.table_created'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label(__('filament.promos.table_status'))
                    ->options(PromoStatus::class),
            ])
            ->defaultSort('priority', 'desc')
            ->recordActions([
                Action::make('preview_json')
                    ->label(__('filament.promos.preview_json'))
                    ->icon('heroicon-o-code-bracket')
                    ->color('info')
                    ->modalHeading(__('filament.promos.preview_heading'))
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel(__('filament.promos.preview_close'))
                    ->modalWidth('2xl')
                    ->form(fn (Promo $record) => [
                        Tabs::make('API Responses')
                            ->tabs([
                                Tab::make(__('filament.promos.preview_success'))
                                    ->icon('heroicon-m-check-circle')
                                    ->schema([
                                        Textarea::make('json_success')
                                            ->label(__('filament.promos.preview_response_body'))
                                            ->rows(12)
                                            ->formatStateUsing(function () use ($record) {
                                                $data = [
                                                    'id' => $record->id,
                                                    'title' => $record->getTranslation('title'),
                                                    'content' => $record->getTranslation('content'),
                                                    'image_url' => $record->full_image_url,
                                                    'cta_text' => $record->getTranslation('cta_text'),
                                                    'cta_url' => $record->cta_url,
                                                    'priority' => $record->priority,
                                                    'max_impressions' => $record->max_impressions,
                                                    'cooldown_seconds' => $record->cooldown_seconds,
                                                    'display_mode' => $record->display_mode,
                                                    'start_date' => $record->starts_at?->format('Y-m-d'),
                                                    'end_date' => $record->ends_at?->format('Y-m-d'),
                                                ];

                                                return json_encode([
                                                    'success' => true,
                                                    'data' => $data,
                                                    'meta' => [],
                                                ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
                                            })
                                            ->disabled(),
                                    ]),
                                Tab::make(__('filament.promos.preview_unauthorized'))
                                    ->icon('heroicon-m-lock-closed')
                                    ->schema([
                                        Textarea::make('json_unauthorized')
                                            ->label(__('filament.promos.preview_response_body'))
                                            ->rows(12)
                                            ->formatStateUsing(function () {
                                                return json_encode([
                                                    'success' => false,
                                                    'error' => [
                                                        'code' => 'UNAUTHORIZED',
                                                        'message' => 'Invalid API key',
                                                        'details' => [],
                                                    ],
                                                ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
                                            })
                                            ->disabled(),
                                    ]),
                                Tab::make(__('filament.promos.preview_not_found'))
                                    ->icon('heroicon-m-x-circle')
                                    ->schema([
                                        Textarea::make('json_error')
                                            ->label(__('filament.promos.preview_response_body'))
                                            ->rows(12)
                                            ->formatStateUsing(function () {
                                                return json_encode([
                                                    'success' => false,
                                                    'error' => [
                                                        'code' => 'NOT_FOUND',
                                                        'message' => 'No active promo available',
                                                        'details' => [],
                                                    ],
                                                ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
                                            })
                                            ->disabled(),
                                    ]),
                                Tab::make(__('filament.promos.preview_rate_limit'))
                                    ->icon('heroicon-m-bolt')
                                    ->schema([
                                        Textarea::make('json_throttle')
                                            ->label(__('filament.promos.preview_response_body'))
                                            ->rows(12)
                                            ->formatStateUsing(function () {
                                                return json_encode([
                                                    'success' => false,
                                                    'error' => [
                                                        'code' => 'RATE_LIMIT_EXCEEDED',
                                                        'message' => 'Too many requests',
                                                        'details' => [],
                                                    ],
                                                ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
                                            })
                                            ->disabled(),
                                    ]),
                            ]),
                    ]),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
