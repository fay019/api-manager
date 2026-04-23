@extends('layouts.app')

@section('content')
<div class="pt-16 pb-10 px-4">
    <div class="w-full max-w-md mx-auto">
        <div class="bg-gradient-to-br from-green-50 to-emerald-50 dark:from-slate-900 dark:to-slate-800 rounded-lg shadow-lg p-6">
            <div class="text-center mb-4">
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">
                    📝 {{ __('client.client_auth.register_title') }}
                </h1>
                <p class="text-gray-600 dark:text-gray-400">
                    {{ __('client.client_auth.register_description') ?? 'Créez votre compte client en quelques étapes' }}
                </p>
            </div>

        <div class="bg-white dark:bg-slate-800 rounded-lg shadow-lg p-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 text-center">
                {{ __('client.client_auth.register_title') }}
            </h2>

            <div class="mb-6 p-3 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg">
                <p class="text-sm text-blue-700 dark:text-blue-300 flex items-start gap-2">
                    <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 5v8a2 2 0 01-2 2h-5l-5 4v-4H4a2 2 0 01-2-2V5a2 2 0 012-2h12a2 2 0 012 2zm-7 4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 100-2 1 1 0 000 2zm5 0a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"></path>
                    </svg>
                    <span>{{ __('client.client_auth.email_validation_required') ?? 'Un email de confirmation sera envoyé. Veuillez utiliser une adresse email valide.' }}</span>
                </p>
            </div>

            <form method="POST" action="{{ route('client.register') }}" class="space-y-6" x-data="{ type: 'person', password: '' }">
                @csrf

                <!-- Type Selector -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
                        {{ __('client.type') ?? 'Account Type' }}
                    </label>
                    <div class="flex gap-4">
                        <label class="flex items-center cursor-pointer">
                            <input
                                type="radio"
                                name="type"
                                value="person"
                                x-model="type"
                                class="w-4 h-4 text-blue-600"
                            >
                            <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">{{ __('client.person') ?? 'Person' }}</span>
                        </label>
                        <label class="flex items-center cursor-pointer">
                            <input
                                type="radio"
                                name="type"
                                value="company"
                                x-model="type"
                                class="w-4 h-4 text-blue-600"
                            >
                            <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">{{ __('client.company') ?? 'Company' }}</span>
                        </label>
                    </div>
                    @error('type')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Company Name (Company only) -->
                <div x-show="type === 'company'" x-transition>
                    <label for="company_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        {{ __('client.company_name') }}
                    </label>
                    <input
                        type="text"
                        id="company_name"
                        name="company_name"
                        value="{{ old('company_name') }}"
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-slate-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
                    >
                    @error('company_name')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- First Name -->
                <div>
                    <label for="first_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        {{ __('client.first_name') }}
                    </label>
                    <input
                        type="text"
                        id="first_name"
                        name="first_name"
                        value="{{ old('first_name') }}"
                        required
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-slate-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
                    >
                    @error('first_name')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Last Name -->
                <div>
                    <label for="last_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        {{ __('client.last_name') }}
                    </label>
                    <input
                        type="text"
                        id="last_name"
                        name="last_name"
                        value="{{ old('last_name') }}"
                        required
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-slate-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
                    >
                    @error('last_name')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        {{ __('client.email') }}
                    </label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        value="{{ old('email') }}"
                        required
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-slate-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
                    >
                    @error('email')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Contact Email (Company only) -->
                <div x-show="type === 'company'" x-transition>
                    <div class="mb-4">
                        <label class="flex items-center gap-2">
                            <input
                                type="checkbox"
                                id="same_contact_email"
                                name="same_contact_email"
                                class="w-4 h-4 text-blue-600 rounded cursor-pointer"
                            >
                            <span class="text-sm text-gray-700 dark:text-gray-300">
                                {{ __('client.use_same_as_main_email') ?? 'Use same email as main email' }}
                            </span>
                        </label>
                    </div>

                    <label for="contact_email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        {{ __('client.contact_email') }} <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="email"
                        id="contact_email"
                        name="contact_email"
                        value="{{ old('contact_email') }}"
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-slate-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
                    >
                    @error('contact_email')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Billing Email (All users) -->
                <div>
                    <div class="mb-4">
                        <label class="flex items-center gap-2">
                            <input
                                type="checkbox"
                                id="same_as_main_email"
                                name="same_as_main_email"
                                class="w-4 h-4 text-blue-600 rounded cursor-pointer"
                            >
                            <span class="text-sm text-gray-700 dark:text-gray-300">
                                {{ __('client.use_same_as_main_email') ?? 'Use same email as main email' }}
                            </span>
                        </label>
                    </div>

                    <label for="billing_email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        {{ __('client.billing_email') }}
                        <span class="text-xs text-gray-500 dark:text-gray-400">(invoicing)</span>
                    </label>
                    <input
                        type="email"
                        id="billing_email"
                        name="billing_email"
                        value="{{ old('billing_email') }}"
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-slate-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:opacity-75 disabled:cursor-not-allowed"
                    >
                    @error('billing_email')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div x-data="{ password: '', confirmPassword: '', showPwd: false }">
                    <!-- Password field -->
                    <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        {{ __('client.password') }}
                    </label>
                    <div class="relative mb-3">
                        <input
                            :type="showPwd ? 'text' : 'password'"
                            id="password"
                            name="password"
                            x-model="password"
                            required
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-slate-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 pr-10"
                        >
                        <button
                            type="button"
                            @click="showPwd = !showPwd"
                            class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 dark:hover:text-gray-300"
                            :title="showPwd ? '{{ __('client.hide_password') }}' : '{{ __('client.show_password') }}'"
                        >
                            <svg x-show="!showPwd" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"></path>
                                <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"></path>
                            </svg>
                            <svg x-show="showPwd" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M3.707 2.293a1 1 0 00-1.414 1.414l14 14a1 1 0 001.414-1.414l-14-14zM11 7a1 1 0 011 1v5a1 1 0 11-2 0V8a1 1 0 011-1zM6 8a1 1 0 00-1 1v2a1 1 0 102 0V9a1 1 0 00-1-1zm8 0a1 1 0 00-1 1v2a1 1 0 102 0V9a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                            </svg>
                        </button>
                    </div>

                    <!-- Password strength checker -->
                    <div class="bg-gray-50 dark:bg-gray-800/50 rounded-lg p-3 space-y-2 mb-4">
                        <div class="flex items-center gap-2">
                            <svg :class="password.length >= 8 ? 'text-green-500' : 'text-gray-300'" class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            <span :class="password.length >= 8 ? 'text-green-600 dark:text-green-400 font-medium' : 'text-gray-600 dark:text-gray-400'" class="text-xs">{{ __('client.password_min_chars') }}</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <svg :class="/[A-Z]/.test(password) ? 'text-green-500' : 'text-gray-300'" class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            <span :class="/[A-Z]/.test(password) ? 'text-green-600 dark:text-green-400 font-medium' : 'text-gray-600 dark:text-gray-400'" class="text-xs">{{ __('client.password_uppercase') }}</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <svg :class="/[a-z]/.test(password) ? 'text-green-500' : 'text-gray-300'" class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            <span :class="/[a-z]/.test(password) ? 'text-green-600 dark:text-green-400 font-medium' : 'text-gray-600 dark:text-gray-400'" class="text-xs">{{ __('client.password_lowercase') }}</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <svg :class="/[0-9]/.test(password) ? 'text-green-500' : 'text-gray-300'" class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            <span :class="/[0-9]/.test(password) ? 'text-green-600 dark:text-green-400 font-medium' : 'text-gray-600 dark:text-gray-400'" class="text-xs">{{ __('client.password_number') }}</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <svg :class="/[^A-Za-z0-9]/.test(password) ? 'text-green-500' : 'text-gray-300'" class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            <span :class="/[^A-Za-z0-9]/.test(password) ? 'text-green-600 dark:text-green-400 font-medium' : 'text-gray-600 dark:text-gray-400'" class="text-xs">{{ __('client.password_symbol') }}</span>
                        </div>
                    </div>

                    @error('password')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror

                    <!-- Password confirmation field -->
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        {{ __('client.password_confirmation') }}
                    </label>
                    <div class="relative mb-2">
                        <input
                            :type="showPwd ? 'text' : 'password'"
                            id="password_confirmation"
                            name="password_confirmation"
                            x-model="confirmPassword"
                            required
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-slate-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 pr-10"
                        >
                        <button
                            type="button"
                            @click="showPwd = !showPwd"
                            class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 dark:hover:text-gray-300"
                            :title="showPwd ? '{{ __('client.hide_password') }}' : '{{ __('client.show_password') }}'"
                        >
                            <svg x-show="!showPwd" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"></path>
                                <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"></path>
                            </svg>
                            <svg x-show="showPwd" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M3.707 2.293a1 1 0 00-1.414 1.414l14 14a1 1 0 001.414-1.414l-14-14zM11 7a1 1 0 011 1v5a1 1 0 11-2 0V8a1 1 0 011-1zM6 8a1 1 0 00-1 1v2a1 1 0 102 0V9a1 1 0 00-1-1zm8 0a1 1 0 00-1 1v2a1 1 0 102 0V9a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                            </svg>
                        </button>
                    </div>

                    <!-- Password match indicator -->
                    <div class="flex items-center gap-2">
                        <svg :class="confirmPassword === password && confirmPassword.length > 0 ? 'text-green-500' : 'text-gray-300'" class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        <span :class="confirmPassword === password && confirmPassword.length > 0 ? 'text-green-600 dark:text-green-400 font-medium' : 'text-gray-600 dark:text-gray-400'" class="text-xs">{{ __('client.password_match') ?? 'Passwords match' }}</span>
                    </div>
                </div>

                <button
                    type="submit"
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg transition-colors"
                >
                    {{ __('client.client_auth.register_button') }}
                </button>
            </form>

            <script>
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

            <div class="mt-6 text-center">
                <p class="text-gray-600 dark:text-gray-400">
                    {{ __('client.client_auth.have_account') }}
                    <a href="{{ route('client.login') }}" class="text-blue-600 hover:text-blue-700 font-semibold">
                        {{ __('client.client_auth.login_link') }}
                    </a>
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
