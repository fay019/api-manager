<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Http\Requests\Client\UpdateProfileRequest;
use App\Models\ApiKey;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function edit(): View
    {
        return view('client.profile.edit', ['client' => auth('client')->user()]);
    }

    public function update(UpdateProfileRequest $request): RedirectResponse
    {
        $client = auth('client')->user();
        $data = $request->safe()->except(['avatar', 'password', 'password_confirmation']);

        if ($request->hasFile('avatar')) {
            if ($client->avatar && Storage::disk('public')->exists($client->avatar)) {
                Storage::disk('public')->delete($client->avatar);
            }

            $extension = $request->file('avatar')->guessExtension();
            $path = $request->file('avatar')->storeAs(
                'avatars',
                Str::uuid().'.'.$extension,
                'public'
            );
            $data['avatar'] = $path;
        }

        if ($request->filled('password')) {
            $data['password'] = $request->input('password');
            Log::info('client.password.changed', ['id' => $client->id]);
        }

        $client->update($data);

        return redirect()->back()->with('success', __('client.client_auth.profile_updated'));
    }

    public function getApiKey(ApiKey $id): JsonResponse
    {
        $client = auth('client')->user();

        $id->load('apiClient');
        if ($id->apiClient->client_id !== $client->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $fullKey = null;
        try {
            $fullKey = Crypt::decryptString($id->key_encrypted);
        } catch (\Exception $e) {
            Log::warning('client.api-key.decrypt.error', ['id' => $id->id, 'email' => $client->email]);
        }

        return response()->json([
            'key' => $fullKey,
            'prefix' => $id->key_prefix,
        ]);
    }
}
