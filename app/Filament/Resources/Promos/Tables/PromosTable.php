<?php

namespace App\Filament\Resources\Promos\Tables;

use App\Enums\PromoStatus;
use App\Models\Promo;
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
                    ->label('Titre')
                    ->searchable()
                    ->sortable(),
                ImageColumn::make('image_url')
                    ->label('Image')
                    ->circular()
                    ->disk('public'),
                TextColumn::make('status')
                    ->label('Statut')
                    ->badge()
                    ->sortable(),
                TextColumn::make('starts_at')
                    ->label('Début')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
                TextColumn::make('ends_at')
                    ->label('Fin')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
                TextColumn::make('priority')
                    ->label('Priorité')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('creator.name')
                    ->label('Créé par')
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('created_at')
                    ->label('Créé le')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Statut')
                    ->options(PromoStatus::class),
            ])
            ->defaultSort('priority', 'desc')
            ->recordActions([
                \Filament\Actions\Action::make('preview_json')
                    ->label('Aperçu JSON')
                    ->icon('heroicon-o-code-bracket')
                    ->color('info')
                    ->modalHeading('Aperçu de la réponse API')
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Fermer')
                    ->modalWidth('2xl')
                    ->form(fn (Promo $record) => [
                        Tabs::make('API Responses')
                            ->tabs([
                                Tab::make('Succès (200 OK)')
                                    ->icon('heroicon-m-check-circle')
                                    ->schema([
                                        Textarea::make('json_success')
                                            ->label('Corps de la réponse')
                                            ->rows(12)
                                            ->formatStateUsing(function () use ($record) {
                                                $data = [
                                                    'id' => $record->id,
                                                    'title' => $record->title,
                                                    'content' => $record->content,
                                                    'image_url' => $record->full_image_url,
                                                    'cta_text' => $record->cta_text,
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
                                Tab::make('Erreur (401 Unauthorized)')
                                    ->icon('heroicon-m-lock-closed')
                                    ->schema([
                                        Textarea::make('json_unauthorized')
                                            ->label('Corps de la réponse')
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
                                Tab::make('Erreur (404 Not Found)')
                                    ->icon('heroicon-m-x-circle')
                                    ->schema([
                                        Textarea::make('json_error')
                                            ->label('Corps de la réponse')
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
                                Tab::make('Erreur (429 Too Many Requests)')
                                    ->icon('heroicon-m-bolt')
                                    ->schema([
                                        Textarea::make('json_throttle')
                                            ->label('Corps de la réponse')
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
                \Filament\Actions\EditAction::make(),
            ])
            ->toolbarActions([
                \Filament\Actions\BulkActionGroup::make([
                    \Filament\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
