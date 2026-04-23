<?php

namespace App\Filament\Resources\Clients\Pages;

use App\Filament\Resources\Clients\ClientResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditClient extends EditRecord
{
    protected static string $resource = ClientResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $client = $this->record;

        // Build address_json from separate fields
        $data['address_json'] = [
            'street' => $data['address_json']['street'] ?? null,
            'city' => $data['address_json']['city'] ?? null,
            'postal_code' => $data['address_json']['postal_code'] ?? null,
        ];

        // Handle billing_email sync
        if (isset($data['same_as_main_email']) && $data['same_as_main_email']) {
            $data['billing_email'] = $data['email'];
        }
        unset($data['same_as_main_email']);

        // Handle contact_email sync (company only)
        if ($client->type === 'company') {
            if (isset($data['same_contact_email']) && $data['same_contact_email']) {
                $data['contact_email'] = $data['email'];
            }
            unset($data['same_contact_email']);
        }

        // Set contact_email to null for persons
        if ($client->type === 'person') {
            $data['contact_email'] = null;
        }

        return $data;
    }
}
