<?php

namespace App\Filament\Resources\Promos\Pages;

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

    public bool $showVersionHistory = false;

    public function getTitle(): string
    {
        $record = $this->getRecord();
        try {
            $latestVersion = $record->versions()->orderBy('version', 'desc')->first();
            $versionNumber = $latestVersion ? $latestVersion->version : 1;

            return "{$record->title} (v{$versionNumber})";
        } catch (\Exception $e) {
            return $record->title ?? 'Edit Promotion';
        }
    }

    public function toggleVersionHistory(): void
    {
        $this->showVersionHistory = ! $this->showVersionHistory;
    }

    /**
     * Get all versions for this promo
     */
    public function getPromoVersions()
    {
        return $this->getRecord()->versions()
            ->orderBy('version', 'desc')
            ->with('creator')
            ->get();
    }

    /**
     * Get version diff
     */
    public function getVersionDiff($v1, $v2): array
    {
        $version1 = $this->getRecord()->versions()->where('version', $v1)->first();
        $version2 = $this->getRecord()->versions()->where('version', $v2)->first();

        if (! $version1 || ! $version2) {
            return [];
        }

        $payload1 = $version1->payload_json ?? [];
        $payload2 = $version2->payload_json ?? [];

        $diff = [];
        $allKeys = array_unique(array_merge(array_keys($payload1), array_keys($payload2)));

        foreach ($allKeys as $key) {
            $old = $payload1[$key] ?? null;
            $new = $payload2[$key] ?? null;

            if ($old !== $new) {
                $diff[$key] = ['old' => $old, 'new' => $new];
            }
        }

        return $diff;
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('view_history')
                ->label('Version History')
                ->icon('heroicon-o-clock')
                ->color('info')
                ->button()
                ->slideOver()
                ->modalHeading('Version History & Changes')
                ->modalDescription('Track all modifications made to this promotion')
                ->modalContent(fn (Promo $record) => view('filament.components.version-history-modal', [
                    'versions' => $record->versions()->orderBy('version', 'desc')->with('creator')->get(),
                    'record' => $record,
                    'page' => $this,
                ]))
                ->modalSubmitAction(false)
                ->modalCancelActionLabel('Close'),
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
            DeleteAction::make(),
        ];
    }
}
