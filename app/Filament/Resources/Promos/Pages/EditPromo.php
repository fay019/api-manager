<?php

namespace App\Filament\Resources\Promos\Pages;

use App\Enums\PromoStatus;
use App\Filament\Resources\Promos\PromoResource;
use App\Models\Promo;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\Textarea;
use Filament\Resources\Pages\EditRecord;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;

class EditPromo extends EditRecord
{
    protected static string $resource = PromoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('preview_json')
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
            DeleteAction::make(),
        ];
    }
}
