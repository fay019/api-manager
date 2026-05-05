<?php

namespace App\Filament\Resources\Clients\Pages;

use App\Filament\Resources\Clients\ClientResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Hash;

class CreateClient extends CreateRecord
{
    protected static string $resource = ClientResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Validate password confirmation
        if (isset($data['password']) && isset($data['password_confirmation'])) {
            if ($data['password'] !== $data['password_confirmation']) {
                throw new \Exception('Les mots de passe ne correspondent pas.');
            }
            $data['password'] = Hash::make($data['password']);
            unset($data['password_confirmation']);
        }

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
        if ($data['type'] === 'company') {
            if (isset($data['same_contact_email']) && $data['same_contact_email']) {
                $data['contact_email'] = $data['email'];
            }
            unset($data['same_contact_email']);
        } else {
            $data['contact_email'] = null;
            unset($data['same_contact_email']);
        }

        return $data;
    }
}
