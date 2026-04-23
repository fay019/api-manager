<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Http\Requests\Client\ClientProfileUpdateRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function edit(): View
    {
        $client = auth('client')->user();

        return view('client.profile.edit', [
            'client' => $client,
            'countries' => $this->getCountries(),
            'timezones' => timezone_identifiers_list(),
        ]);
    }

    public function update(ClientProfileUpdateRequest $request)
    {
        $client = auth('client')->user();

        $billingEmail = $request->boolean('same_as_main_email')
            ? $request->email
            : $request->billing_email;

        $contactEmail = null;
        $contactName = null;
        if ($client->type === 'company') {
            $contactEmail = $request->boolean('same_contact_email')
                ? $request->email
                : $request->contact_email;
            $contactName = $request->contact_name;
        }

        $data = [
            'email' => $request->email,
            'phone' => $request->phone,
            'country' => $request->country,
            'timezone' => $request->timezone,
            'language' => $request->language,
            'billing_email' => $billingEmail,
            'address_json' => [
                'street' => $request->address,
                'city' => $request->city,
                'postal_code' => $request->postal_code,
            ],
        ];

        if ($client->type === 'person') {
            $data['first_name'] = $request->first_name;
            $data['last_name'] = $request->last_name;
        } else {
            $data['company_name'] = $request->company_name;
            $data['description'] = $request->description;
            $data['contact_name'] = $contactName;
            $data['contact_email'] = $contactEmail;
        }

        $client->update($data);

        Log::info('client.profile.updated', ['id' => $client->id]);

        return redirect()->route('client.profile.edit')
            ->with('success', __('client.profile.updated'));
    }

    public function updateAvatar(Request $request): JsonResponse
    {
        $request->validate([
            'avatar' => ['required', 'image', 'max:5120'],
        ]);

        $client = auth('client')->user();

        if ($client->avatar && Storage::exists('public/'.$client->avatar)) {
            Storage::delete('public/'.$client->avatar);
        }

        $path = $request->file('avatar')->store('avatars', 'public');

        $client->update(['avatar' => $path]);

        Log::info('client.avatar.updated', ['id' => $client->id]);

        return response()->json(['message' => __('client.profile.avatar_updated')]);
    }

    public function getApiKey(int $apiKeyId)
    {
        $client = auth('client')->user();

        $apiKey = $client->apiClients()
            ->with('apiKeys')
            ->get()
            ->pluck('apiKeys')
            ->flatten()
            ->where('id', $apiKeyId)
            ->firstOrFail();

        return response()->json(['key' => decrypt($apiKey->key_encrypted)]);
    }

    private function getCountries(): array
    {
        return [
            'DZ' => 'Algeria',
            'FR' => 'France',
            'DE' => 'Germany',
            'GB' => 'United Kingdom',
            'IT' => 'Italy',
            'ES' => 'Spain',
            'NL' => 'Netherlands',
            'BE' => 'Belgium',
            'AT' => 'Austria',
            'CH' => 'Switzerland',
            'SE' => 'Sweden',
            'NO' => 'Norway',
            'DK' => 'Denmark',
            'FI' => 'Finland',
            'PL' => 'Poland',
            'CZ' => 'Czech Republic',
            'US' => 'United States',
            'CA' => 'Canada',
            'MX' => 'Mexico',
            'BR' => 'Brazil',
            'AU' => 'Australia',
            'NZ' => 'New Zealand',
            'JP' => 'Japan',
            'CN' => 'China',
            'IN' => 'India',
            'SG' => 'Singapore',
        ];
    }
}
