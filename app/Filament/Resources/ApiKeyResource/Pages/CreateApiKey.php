<?php

namespace App\Filament\Resources\ApiKeyResource\Pages;

use App\Filament\Resources\ApiKeyResource;
use App\Services\ApiKeyService;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateApiKey extends CreateRecord
{
    protected static string $resource = ApiKeyResource::class;

    protected ?string $rawKey = null;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $generatedKey = (new ApiKeyService)->generateKey();

        $data['key_encrypted'] = $generatedKey['encrypted'];
        $data['key_prefix'] = $generatedKey['prefix'];

        // Store for notification
        $this->rawKey = $generatedKey['raw'];

        return $data;
    }

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Clé API générée')
            ->body("Voici votre clé API :  \n`{$this->rawKey}`  \n\n**Note :** Conservez-la précieusement, elle ne sera plus affichée par la suite.")
            ->persistent()
            ->actions([
                \Filament\Actions\Action::make('copy')
                    ->label('Copier la clé')
                    ->color('gray')
                    ->url('#')
                    ->extraAttributes([
                        'onclick' => "
                            const text = '{$this->rawKey}';
                            if (navigator.clipboard && window.isSecureContext) {
                                navigator.clipboard.writeText(text).then(() => {
                                    alert('Clé copiée dans le presse-papier !');
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
                                    alert('Clé copiée dans le presse-papier !');
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
    }
}
