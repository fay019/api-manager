@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-slate-900 py-12 px-4">
    <div class="max-w-2xl mx-auto">
        <div class="bg-white dark:bg-slate-800 rounded-lg shadow-lg p-8">
            <!-- Header with title and avatar -->
            <div class="flex justify-between items-start mb-8">
                <div class="flex items-center gap-3">
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
                        {{ __('client.profile.title') }}
                    </h1>
                    <span class="px-3 py-1 rounded-full text-sm font-semibold {{ $client->type === 'company' ? 'bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200' : 'bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200' }}">
                        {{ $client->type === 'company' ? __('client.company') : __('client.person') }}
                    </span>
                </div>

                <!-- Avatar section -->
                <div class="relative group">
                    <div class="w-24 h-24 rounded-full bg-gray-200 dark:bg-gray-700 overflow-hidden border-4 border-gray-300 dark:border-gray-600">
                        @if ($client->avatar)
                            <img src="{{ asset('storage/' . $client->avatar) }}" alt="{{ $client->name }}" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-blue-400 to-blue-600">
                                <span class="text-white text-3xl font-bold">
                                    {{ strtoupper(substr($client->first_name, 0, 1)) }}{{ strtoupper(substr($client->last_name, 0, 1)) }}
                                </span>
                            </div>
                        @endif
                    </div>

                    <!-- Edit button -->
                    <button
                        type="button"
                        onclick="document.getElementById('avatar-input').click()"
                        class="absolute bottom-0 right-0 bg-blue-600 hover:bg-blue-700 text-white rounded-full p-2 shadow-lg transition-colors"
                        title="{{ __('client.profile.change_avatar') }}"
                    >
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"></path>
                        </svg>
                    </button>

                    <!-- Hidden file input -->
                    <input
                        type="file"
                        id="avatar-input"
                        name="avatar"
                        accept="image/*"
                        class="hidden"
                        onchange="submitAvatarForm(this)"
                    >
                </div>
            </div>

            @if ($errors->any())
                <div class="mb-6 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4">
                    <h3 class="text-red-800 dark:text-red-200 font-semibold mb-2">{{ __('client.profile.errors') }}</h3>
                    <ul class="list-disc list-inside space-y-1">
                        @foreach ($errors->all() as $error)
                            <li class="text-red-700 dark:text-red-300 text-sm">{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if (session('success'))
                <div class="mb-6 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-4">
                    <p class="text-green-700 dark:text-green-300">{{ session('success') }}</p>
                </div>
            @endif

            <!-- Hidden avatar form -->
            <form id="avatar-form" method="POST" action="{{ route('client.profile.update-avatar') }}" enctype="multipart/form-data" class="hidden">
                @csrf
                @method('POST')
                <input type="file" id="avatar-file-input" name="avatar" accept="image/*">
            </form>

            <form method="POST" action="{{ route('client.profile.update') }}" class="space-y-6">
                @csrf
                @method('PUT')

                <!-- Personal/Contact Information -->
                <div class="border-b border-gray-200 dark:border-gray-700 pb-6">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                        @if ($client->type === 'company')
                            {{ __('client.profile.contact_person') ?? 'Contact principal' }}
                        @else
                            {{ __('client.profile.personal_info') }}
                        @endif
                    </h2>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="first_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                {{ __('client.first_name') }} <span class="text-red-500">*</span>
                            </label>
                            <input
                                type="text"
                                id="first_name"
                                name="first_name"
                                value="{{ old('first_name', $client->first_name) }}"
                                required
                                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-slate-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
                            >
                            @error('first_name')
                                <p class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="last_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                {{ __('client.last_name') }} <span class="text-red-500">*</span>
                            </label>
                            <input
                                type="text"
                                id="last_name"
                                name="last_name"
                                value="{{ old('last_name', $client->last_name) }}"
                                required
                                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-slate-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
                            >
                            @error('last_name')
                                <p class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="mt-4">
                        <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            {{ __('client.email') }}
                        </label>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            value="{{ old('email', $client->email) }}"
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-slate-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
                            required
                        >
                        @error('email')
                            <p class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Company Information (Company only) -->
                @if ($client->type === 'company')
                <div class="border-b border-gray-200 dark:border-gray-700 pb-6">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                        {{ __('client.profile.company_info') }}
                    </h2>

                    <div>
                        <label for="company_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            {{ __('client.company_name') }} <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="text"
                            id="company_name"
                            name="company_name"
                            value="{{ old('company_name', $client->company_name) }}"
                            required
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-slate-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
                        >
                        @error('company_name')
                            <p class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mt-4">
                        <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            {{ __('client.description') }}
                        </label>
                        <textarea
                            id="description"
                            name="description"
                            rows="4"
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-slate-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="{{ __('client.profile.description_placeholder') }}"
                        >{{ old('description', $client->description) }}</textarea>
                        @error('description')
                            <p class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                @endif

                <!-- Billing Email (All users) -->
                <div class="border-b border-gray-200 dark:border-gray-700 pb-6">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                        {{ __('client.billing_email') }}
                    </h2>

                    <div>
                        <label class="flex items-center gap-2 mb-3">
                            <input
                                type="checkbox"
                                id="same_as_main_email"
                                name="same_as_main_email"
                                {{ old('same_as_main_email', $client->billing_email === $client->email ? 'checked' : '') ? 'checked' : '' }}
                                class="w-4 h-4 text-blue-600 rounded cursor-pointer"
                            >
                            <span class="text-sm text-gray-700 dark:text-gray-300">
                                {{ __('client.use_same_as_main_email') ?? 'Use same email as main email' }}
                            </span>
                        </label>

                        <label for="billing_email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            {{ __('client.billing_email') }}
                            <span class="text-xs text-gray-500 dark:text-gray-400">(invoicing)</span>
                        </label>
                        <input
                            type="email"
                            id="billing_email"
                            name="billing_email"
                            value="{{ old('billing_email', $client->billing_email ?? $client->email) }}"
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-slate-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:opacity-75 disabled:cursor-not-allowed"
                        >
                        @error('billing_email')
                            <p class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Contact Information -->
                <div class="border-b border-gray-200 dark:border-gray-700 pb-6">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                        {{ __('client.profile.contact_info') }}
                    </h2>

                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            {{ __('client.phone') }}
                        </label>
                        <input
                            type="tel"
                            id="phone"
                            name="phone"
                            value="{{ old('phone', $client->phone) }}"
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-slate-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
                        >
                        @error('phone')
                            <p class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    @if ($client->type === 'company')
                    <div class="mt-4">
                        <label class="flex items-center gap-2 mb-3">
                            <input
                                type="checkbox"
                                id="same_contact_email"
                                name="same_contact_email"
                                {{ old('same_contact_email', $client->contact_email === $client->email ? 'checked' : '') ? 'checked' : '' }}
                                class="w-4 h-4 text-blue-600 rounded cursor-pointer"
                            >
                            <span class="text-sm text-gray-700 dark:text-gray-300">
                                {{ __('client.use_same_as_main_email') ?? 'Use same email as main email' }}
                            </span>
                        </label>

                        <label for="contact_email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            {{ __('client.contact_email') }} <span class="text-red-500">*</span>
                            <span class="text-xs text-gray-500 dark:text-gray-400">(main contact)</span>
                        </label>
                        <input
                            type="email"
                            id="contact_email"
                            name="contact_email"
                            value="{{ old('contact_email', $client->contact_email) }}"
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-slate-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:opacity-75 disabled:cursor-not-allowed"
                        >
                        @error('contact_email')
                            <p class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    @endif

                    <div class="mt-4">
                        <label for="address" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            {{ __('client.profile.address') }}
                        </label>
                        <input
                            type="text"
                            id="address"
                            name="address"
                            value="{{ old('address', $client->address_json['street'] ?? '') }}"
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-slate-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
                        >
                        @error('address')
                            <p class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-4 mt-4">
                        <div>
                            <label for="city" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                {{ __('client.profile.city') }}
                            </label>
                            <input
                                type="text"
                                id="city"
                                name="city"
                                value="{{ old('city', $client->address_json['city'] ?? '') }}"
                                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-slate-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
                            >
                            @error('city')
                                <p class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="postal_code" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                {{ __('client.profile.postal_code') }}
                            </label>
                            <input
                                type="text"
                                id="postal_code"
                                name="postal_code"
                                value="{{ old('postal_code', $client->address_json['postal_code'] ?? '') }}"
                                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-slate-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
                            >
                            @error('postal_code')
                                <p class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="mt-4">
                        <label for="country" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            {{ __('client.country') }}
                        </label>
                        <select
                            id="country"
                            name="country"
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-slate-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
                        >
                            @foreach ($countries as $code => $name)
                                <option value="{{ $code }}" {{ old('country', $client->country) === $code ? 'selected' : '' }}>
                                    {{ $name }}
                                </option>
                            @endforeach
                        </select>
                        @error('country')
                            <p class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Preferences -->
                <div class="pb-6">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                        {{ __('client.profile.preferences') }}
                    </h2>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="timezone" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                {{ __('client.timezone') }}
                            </label>
                            <select
                                id="timezone"
                                name="timezone"
                                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-slate-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
                                required
                            >
                                @foreach ($timezones as $tz)
                                    <option value="{{ $tz }}" {{ old('timezone', $client->timezone) === $tz ? 'selected' : '' }}>
                                        {{ $tz }}
                                    </option>
                                @endforeach
                            </select>
                            @error('timezone')
                                <p class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="language" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                {{ __('client.language') }}
                            </label>
                            <select
                                id="language"
                                name="language"
                                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-slate-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
                                required
                            >
                                <option value="fr" {{ old('language', $client->language) === 'fr' ? 'selected' : '' }}>Français</option>
                                <option value="en" {{ old('language', $client->language) === 'en' ? 'selected' : '' }}>English</option>
                                <option value="de" {{ old('language', $client->language) === 'de' ? 'selected' : '' }}>Deutsch</option>
                            </select>
                            @error('language')
                                <p class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Buttons -->
                <div class="flex gap-4">
                    <button
                        type="submit"
                        class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg transition-colors"
                    >
                        {{ __('client.profile.save') }}
                    </button>
                    <a
                        href="{{ route('client.dashboard') }}"
                        class="flex-1 bg-gray-300 dark:bg-gray-600 hover:bg-gray-400 dark:hover:bg-gray-700 text-gray-900 dark:text-white font-semibold py-2 px-4 rounded-lg transition-colors text-center"
                    >
                        {{ __('client.profile.cancel') }}
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function submitAvatarForm(input) {
    if (input.files && input.files[0]) {
        const formData = new FormData();
        formData.append('avatar', input.files[0]);
        formData.append('_token', document.querySelector('input[name="_token"]').value);

        fetch('{{ route("client.profile.update-avatar") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => {
            if (response.ok) {
                location.reload();
            }
        })
        .catch(error => console.error('Error:', error));
    }
}

document.addEventListener('DOMContentLoaded', function () {
    const mainEmail = document.querySelector('input[name="email"]');

    // Billing Email Sync
    const billingCheckbox = document.getElementById('same_as_main_email');
    const billingInput = document.getElementById('billing_email');

    function updateBilling() {
        if (billingCheckbox && billingCheckbox.checked) {
            billingInput.value = mainEmail.value;
            billingInput.readOnly = true;
            billingInput.classList.add('bg-gray-100', 'dark:bg-gray-600', 'cursor-not-allowed');
        } else if (billingCheckbox) {
            billingInput.readOnly = false;
            billingInput.classList.remove('bg-gray-100', 'dark:bg-gray-600', 'cursor-not-allowed');
        }
    }

    if (billingCheckbox) {
        billingCheckbox.addEventListener('change', function () {
            updateBilling();
        });
        mainEmail.addEventListener('input', updateBilling);
        updateBilling();
    }

    // Contact Email Sync (Company only)
    const contactCheckbox = document.getElementById('same_contact_email');
    const contactInput = document.getElementById('contact_email');

    function updateContact() {
        if (contactCheckbox && contactCheckbox.checked) {
            contactInput.value = mainEmail.value;
            contactInput.readOnly = true;
            contactInput.classList.add('bg-gray-100', 'dark:bg-gray-600', 'cursor-not-allowed');
        } else if (contactCheckbox) {
            contactInput.readOnly = false;
            contactInput.classList.remove('bg-gray-100', 'dark:bg-gray-600', 'cursor-not-allowed');
        }
    }

    if (contactCheckbox) {
        contactCheckbox.addEventListener('change', function () {
            updateContact();
        });
        mainEmail.addEventListener('input', updateContact);
        updateContact();
    }
});
</script>
@endsection
