<?php

namespace App\Filament\Resources\ApiKeyResource\Pages;

use App\Filament\Resources\ApiKeyResource;
use App\Models\ApiKey;
use App\Services\ApiKeyService;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Filament\Actions\Action;

class EditApiKey extends EditRecord
{
    protected static string $resource = ApiKeyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\Action::make('regenerate')
                ->label('Régénérer la clé')
                ->icon('heroicon-m-arrow-path')
                ->color('warning')
                ->requiresConfirmation()
                ->modalHeading('Régénérer la clé API')
                ->modalDescription('Êtes-vous sûr de vouloir régénérer cette clé ? L\'ancienne clé cessera immédiatement de fonctionner.')
                ->modalSubmitActionLabel('Régénérer')
                ->action(function (ApiKey $record) {
                    $generatedKey = (new ApiKeyService())->generateKey();

                    $record->update([
                        'key_encrypted' => $generatedKey['encrypted'],
                        'key_prefix' => $generatedKey['prefix'],
                        'is_active' => true,
                    ]);

                    Notification::make()
                        ->success()
                        ->title('Clé API régénérée')
                        ->body("Voici votre nouvelle clé API :  \n`{$generatedKey['raw']}`  \n\n**Note :** Conservez-la précieusement.")
                        ->persistent()
                        ->actions([
                            \Filament\Actions\Action::make('copy')
                                ->label('Copier la clé')
                                ->color('gray')
                                ->url('#')
                                ->extraAttributes([
                                    'onclick' => "
                                        const text = '{$generatedKey['raw']}';
                                        if (navigator.clipboard && window.isSecureContext) {
                                            navigator.clipboard.writeText(text).then(() => {
                                                alert('Clé copiée !');
                                            }).catch(err => {
                                                console.error('Erreur lors de la copie : ', err);
                                            });
                                        } else {
                                            const textArea = document.createElement('textarea');
                                            textArea.value = text;
                                            textArea.style.position = 'fixed';
                                            textArea.style.left = '-9999px';
                                            textArea.style.top = '0';
                                            document.body.appendChild(textArea);
                                            textArea.focus();
                                            textArea.select();
                                            try {
                                                document.execCommand('copy');
                                                alert('Clé copiée !');
                                            } catch (err) {
                                                console.error('Erreur lors de la copie (fallback) : ', err);
                                            }
                                            document.body.removeChild(textArea);
                                        }
                                        return false;
                                    ",
                                ]),
                        ])
                        ->send();

                    $this->refreshFormData(['key_prefix']);
                }),
            \Filament\Actions\DeleteAction::make(),
        ];
    }
}
