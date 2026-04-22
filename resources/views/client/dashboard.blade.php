@extends('layouts.app')

@section('content')
<div class="min-h-screen px-4 py-12">
    <div class="max-w-6xl mx-auto">
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
                {{ __('client.client_auth.dashboard_title') }}
            </h1>
            <a href="{{ route('client.profile.edit') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg transition-colors">
                {{ __('client.edit') }}
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white dark:bg-slate-800 rounded-lg shadow p-6">
                <h3 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase">
                    {{ __('client.client.name') }}
                </h3>
                <p class="text-2xl font-bold text-gray-900 dark:text-white mt-2">
                    {{ $client->name }}
                </p>
            </div>

            <div class="bg-white dark:bg-slate-800 rounded-lg shadow p-6">
                <h3 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase">
                    {{ __('client.client.contact_email') }}
                </h3>
                <p class="text-lg text-gray-900 dark:text-white mt-2">
                    {{ $client->contact_email ?? $client->email }}
                </p>
            </div>

            <div class="bg-white dark:bg-slate-800 rounded-lg shadow p-6">
                <h3 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase">
                    {{ __('client.client.last_login') }}
                </h3>
                <p class="text-lg text-gray-900 dark:text-white mt-2">
                    @if ($client->last_login_at)
                        {{ $client->last_login_at->format('d/m/Y H:i') }}
                    @else
                        {{ __('client.never') }}
                    @endif
                </p>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-800 rounded-lg shadow p-6" x-data="{ expanded: {} }">
            <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-6">
                {{ __('client.client_auth.my_applications') }}
            </h2>

            @if ($client->apiClients->isEmpty())
                <p class="text-gray-600 dark:text-gray-400 py-6 text-center">
                    {{ __('filament.no_items') }}
                </p>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b border-gray-200 dark:border-gray-700">
                                <th class="text-left py-3 px-4 font-semibold text-gray-900 dark:text-white">
                                    {{ __('client.api_client.name') }}
                                </th>
                                <th class="text-center py-3 px-4 font-semibold text-gray-900 dark:text-white">
                                    {{ __('client.client_auth.active_keys') }}
                                </th>
                                <th class="text-center py-3 px-4 font-semibold text-gray-900 dark:text-white">
                                    {{ __('client.client_auth.total_requests') }}
                                </th>
                                <th class="text-center py-3 px-4 font-semibold text-gray-900 dark:text-white">
                                    {{ __('client.client_auth.success_requests') }}
                                </th>
                                <th class="text-center py-3 px-4 font-semibold text-gray-900 dark:text-white">
                                    {{ __('client.status') }}
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($client->apiClients as $app)
                                <tr class="border-b border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-slate-700 cursor-pointer" @click="expanded[{{ $loop->index }}] = !expanded[{{ $loop->index }}]">
                                    <td class="py-3 px-4 text-gray-900 dark:text-white">
                                        <strong>{{ $app->name }}</strong>
                                        @if ($app->apiKeys->count() > 0)
                                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $app->apiKeys->count() }} clé(s)</p>
                                        @endif
                                    </td>
                                    <td class="py-3 px-4 text-center text-gray-600 dark:text-gray-400">
                                        {{ $app->active_keys_count }}
                                    </td>
                                    <td class="py-3 px-4 text-center text-gray-600 dark:text-gray-400">
                                        {{ $app->total_requests }}
                                    </td>
                                    <td class="py-3 px-4 text-center text-gray-600 dark:text-gray-400">
                                        {{ $app->success_requests }}
                                    </td>
                                    <td class="py-3 px-4 text-center">
                                        @if ($app->is_active)
                                            <span class="inline-block px-3 py-1 bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200 rounded-full text-xs font-semibold">
                                                {{ __('client.active') }}
                                            </span>
                                        @else
                                            <span class="inline-block px-3 py-1 bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-300 rounded-full text-xs font-semibold">
                                                {{ __('client.inactive') }}
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                                @if ($app->apiKeys->count() > 0)
                                    <tr x-show="expanded[{{ $loop->index }}]" class="bg-gray-50 dark:bg-slate-700/50 border-b border-gray-200 dark:border-gray-700">
                                        <td colspan="5" class="py-4 px-4">
                                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                                @foreach ($app->apiKeys as $key)
                                                    <div class="bg-white dark:bg-slate-800 rounded border border-gray-200 dark:border-gray-700 p-4" x-data="apiKeyComponent({{ $key->id }})">
                                                        <div class="flex justify-between items-start mb-3">
                                                            <h4 class="font-semibold text-gray-900 dark:text-white text-sm">{{ $key->name }}</h4>
                                                            @if ($key->is_active)
                                                                <span class="bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200 text-xs font-semibold px-2 py-1 rounded">Actif</span>
                                                            @else
                                                                <span class="bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-300 text-xs font-semibold px-2 py-1 rounded">Inactif</span>
                                                            @endif
                                                        </div>

                                                        <div class="mb-3 p-2 bg-gray-100 dark:bg-slate-900 rounded border border-gray-200 dark:border-gray-700">
                                                            <div class="flex items-center justify-between gap-2">
                                                                <code
                                                                    class="text-xs text-gray-700 dark:text-gray-300 font-mono break-all"
                                                                    x-text="displayKey"
                                                                ></code>
                                                                <div class="flex gap-1 flex-shrink-0">
                                                                    <button
                                                                        @click="toggleKey"
                                                                        :disabled="loading"
                                                                        :class="{ 'opacity-50 cursor-not-allowed': loading }"
                                                                        class="p-1 text-gray-600 dark:text-gray-400 hover:bg-gray-200 dark:hover:bg-slate-800 rounded text-xs transition-opacity"
                                                                        :title="showKey ? '{{ __('client.hide_password') }}' : '{{ __('client.show_password') }}'"
                                                                    >
                                                                        <svg x-show="!showKey" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                                        </svg>
                                                                        <svg x-show="showKey" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path>
                                                                        </svg>
                                                                    </button>
                                                                    <button
                                                                        @click="copyKey"
                                                                        :disabled="loading"
                                                                        :class="{ 'opacity-50 cursor-not-allowed': loading }"
                                                                        class="p-1 text-gray-600 dark:text-gray-400 hover:bg-gray-200 dark:hover:bg-slate-800 rounded text-xs transition-opacity"
                                                                        title="Copier"
                                                                    >
                                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                                                        </svg>
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        @if ($key->last_used_at)
                                                            <p class="text-xs text-gray-500 dark:text-gray-500">Utilisé: {{ $key->last_used_at->diffForHumans() }}</p>
                                                        @else
                                                            <p class="text-xs text-gray-500 dark:text-gray-500">Jamais utilisé</p>
                                                        @endif
                                                    </div>
                                                @endforeach
                                            </div>
                                        </td>
                                    </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        <div class="mt-8">
            <form method="POST" action="{{ route('client.logout') }}" class="text-center">
                @csrf
                <button
                    type="submit"
                    class="bg-red-600 hover:bg-red-700 text-white font-semibold py-2 px-6 rounded-lg transition-colors"
                >
                    {{ __('client.logout') }}
                </button>
            </form>
        </div>
    </div>
</div>

<script>
function apiKeyComponent(keyId) {
    const apiEndpoint = `{{ url('/client/api-keys') }}`;

    return {
        showKey: false,
        loading: false,
        fullKey: null,
        maskedKey: null,
        keyId: keyId,

        init() {
            this.fetchKeyMetadata();
        },

        getKeyUrl() {
            return `${apiEndpoint}/${this.keyId}/key`;
        },

        async fetchKeyMetadata() {
            try {
                const response = await fetch(this.getKeyUrl());
                if (!response.ok) throw new Error('Failed to fetch key metadata');
                const data = await response.json();
                this.maskedKey = data.prefix + '***';
            } catch (error) {
                console.error('Error fetching key metadata:', error);
                this.maskedKey = this.extractMaskedFromEncrypted();
            }
        },

        extractMaskedFromEncrypted() {
            // Try to extract prefix from key_encrypted if it's stored as "prefix+key"
            // If extraction fails, show masked placeholder
            return '***';
        },

        async toggleKey() {
            if (this.showKey) {
                this.showKey = false;
                return;
            }

            this.loading = true;
            try {
                const response = await fetch(this.getKeyUrl());
                if (!response.ok) throw new Error('Failed to fetch API key');
                const data = await response.json();
                if (!data.key) {
                    alert('{{ __('client.client_auth.decrypt_error') }}');
                    return;
                }
                this.fullKey = data.key;
                this.showKey = true;
            } catch (error) {
                console.error('Error fetching API key:', error);
                alert('{{ __('client.client_auth.decrypt_error') }}');
            } finally {
                this.loading = false;
            }
        },

        async copyKey() {
            if (!this.fullKey) {
                this.loading = true;
                try {
                    const response = await fetch(this.getKeyUrl());
                    if (!response.ok) throw new Error('Failed to fetch API key');
                    const data = await response.json();
                    if (!data.key) {
                        alert('{{ __('client.client_auth.decrypt_error') }}');
                        return;
                    }
                    this.fullKey = data.key;
                } catch (error) {
                    console.error('Error fetching API key:', error);
                    alert('{{ __('client.client_auth.decrypt_error') }}');
                    return;
                } finally {
                    this.loading = false;
                }
            }

            if (navigator.clipboard && window.isSecureContext) {
                navigator.clipboard.writeText(this.fullKey).then(() => {
                    console.log('API key copied to clipboard');
                }).catch(error => {
                    console.error('Failed to copy to clipboard:', error);
                    this.fallbackCopy();
                });
            } else {
                this.fallbackCopy();
            }
        },

        fallbackCopy() {
            const textarea = document.createElement('textarea');
            textarea.value = this.fullKey;
            document.body.appendChild(textarea);
            textarea.select();
            document.execCommand('copy');
            document.body.removeChild(textarea);
            console.log('API key copied to clipboard (fallback)');
        },

        get displayKey() {
            if (this.showKey && this.fullKey) {
                return this.fullKey;
            }
            return this.maskedKey || '***';
        }
    }
}
</script>
@endsection
