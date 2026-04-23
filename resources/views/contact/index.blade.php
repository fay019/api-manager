@extends('layouts.app')

@section('title', __('contact.page_title'))

@section('content')
<div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
    <!-- Header -->
    <div class="mb-12 text-center">
        <h1 class="text-5xl sm:text-6xl font-bold text-gray-900 dark:text-white mb-4">
            {{ __('contact.page_title') }}
        </h1>
        <p class="text-lg text-gray-600 dark:text-gray-400 max-w-xl mx-auto">
            {{ __('contact.subtitle') }}
        </p>
    </div>

    <!-- Error Alert -->
    @if ($errors->has('error'))
        <div class="mb-6 p-4 rounded-lg animate-slidedown border-l-4 border-red-500 dark:border-red-400 bg-red-50 dark:bg-red-950/30">
            <div class="flex items-start gap-3">
                <span class="text-2xl flex-shrink-0">❌</span>
                <div>
                    <h3 class="font-semibold text-red-900 dark:text-red-200 mb-1">{{ __('contact.error_title') }}</h3>
                    <p class="text-red-800 dark:text-red-300 text-sm">{{ $errors->first('error') }}</p>
                </div>
            </div>
        </div>
    @endif

    <!-- Success Alert -->
    @if (session('success'))
        <div class="mb-6 p-4 rounded-lg animate-slidedown border-l-4 border-green-500 dark:border-green-400 bg-green-50 dark:bg-green-950/30">
            <div class="flex items-start gap-3">
                <span class="text-2xl flex-shrink-0">✅</span>
                <div>
                    <h3 class="font-semibold text-green-900 dark:text-green-200 mb-1">{{ __('contact.success_title') }}</h3>
                    <p class="text-green-800 dark:text-green-300 text-sm">{{ session('success') }}</p>
                </div>
            </div>
        </div>
    @endif

    <!-- Authenticated Client Badge -->
    @if($client)
        <div class="mb-6 p-4 rounded-lg border-l-4 border-blue-500 dark:border-blue-400 bg-blue-50 dark:bg-blue-950/30">
            <div class="flex items-start gap-3">
                <span class="text-2xl flex-shrink-0">✓</span>
                <div>
                    <h3 class="font-semibold text-blue-900 dark:text-blue-200 mb-1">
                        {{ __('contact.authenticated_title') }}
                    </h3>
                    <p class="text-blue-800 dark:text-blue-300 text-sm">
                        {{ __('contact.authenticated_message', [
                            'name' => $client->type === 'company' ? $client->company_name : trim($client->first_name.' '.$client->last_name)
                        ]) }}
                    </p>
                </div>
            </div>
        </div>
    @endif

    <!-- Contact Form -->
    <form action="{{ route('contact.store') }}" method="POST" class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-8 sm:p-10 shadow-sm" x-data="{
        isAuthenticated: {{ $client ? 'true' : 'false' }},
        type: '{{ old('type', $client?->type ?? 'person') }}',
        useSameEmail: {{ old('use_same_email') ? 'true' : ($client && $client->type === 'company' && !$client->contact_email ? 'true' : 'false') }},
        validation: {
            name: false,
            company_name: false,
            email: false,
            contact_name: false,
            contact_email: false,
            subject: false,
            message: false
        },
        validateEmail(email) {
            return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
        },
        validateName(name, type) {
            if (!name || name.length < 3) return false;
            const max = type === 'company' ? 100 : 50;
            if (name.length > max) return false;
            if (type === 'company') {
                return /^[\p{L}\s\-'0-9]+$/u.test(name);
            }
            return /^[\p{L}\s\-']+$/u.test(name);
        },
        validateSubject(subject) {
            return subject && subject.length >= 5 && subject.length <= 100;
        },
        validateMessage(message) {
            return message && message.length >= 10 && message.length <= 2000;
        },
        init() {
            const formInputs = this.$el.querySelectorAll('input[type=text], input[type=email], textarea');
            formInputs.forEach(input => {
                if (input.value && input.name) {
                    if (input.name === 'name') {
                        this.validation.name = this.validateName(input.value, 'person');
                    } else if (input.name === 'company_name') {
                        this.validation.company_name = this.validateName(input.value, 'company');
                    } else if (input.name === 'email') {
                        this.validation.email = this.validateEmail(input.value);
                    } else if (input.name === 'contact_name') {
                        this.validation.contact_name = this.validateName(input.value, 'person');
                    } else if (input.name === 'contact_email') {
                        this.validation.contact_email = input.value.length === 0 || this.validateEmail(input.value);
                    } else if (input.name === 'subject') {
                        this.validation.subject = this.validateSubject(input.value);
                    } else if (input.name === 'message') {
                        this.validation.message = this.validateMessage(input.value);
                    }
                }
            });
        }
    }" x-init="init()">
        @csrf

        <!-- Honeypot field (hidden from users) -->
        <input type="text" name="website" class="hidden" tabindex="-1" autocomplete="off">

        <!-- Timestamp field (hidden) -->
        <input type="hidden" name="form_timestamp" value="{{ time() }}">

        <!-- Language field (hidden) -->
        <input type="hidden" name="language" value="{{ app()->getLocale() }}">

        <!-- Type field (hidden for authenticated clients) -->
        @if(!$client)
            <div class="mb-6">
                <label class="block text-sm font-semibold text-gray-900 dark:text-white mb-3">
                    {{ __('contact.account_type') }} <span class="text-red-500">*</span>
                </label>
                <div class="space-y-3">
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input
                            type="radio"
                            name="type"
                            value="person"
                            x-model="type"
                            class="w-4 h-4 text-indigo-600 bg-gray-100 border-gray-300 rounded focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:ring-offset-gray-800 dark:bg-gray-700 dark:border-gray-600"
                            required
                        >
                        <span class="text-gray-700 dark:text-gray-300">{{ __('contact.type_person') }}</span>
                    </label>
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input
                            type="radio"
                            name="type"
                            value="company"
                            x-model="type"
                            class="w-4 h-4 text-indigo-600 bg-gray-100 border-gray-300 rounded focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:ring-offset-gray-800 dark:bg-gray-700 dark:border-gray-600"
                            required
                        >
                        <span class="text-gray-700 dark:text-gray-300">{{ __('contact.type_company') }}</span>
                    </label>
                </div>
                @error('type')
                    <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>
        @else
            <input type="hidden" name="type" value="{{ $client->type }}">
        @endif

        <!-- Name Field (Person only) -->
        <div class="mb-6" x-show="type === 'person'">
            <div class="flex items-center justify-between mb-2">
                <label for="name" class="block text-sm font-semibold text-gray-900 dark:text-white">
                    {{ __('contact.name') }} <span class="text-red-500">*</span>
                </label>
                <span x-show="validation.name" class="text-green-500 text-lg">✓</span>
            </div>
            <input
                type="text"
                id="name"
                name="name"
                @input="validation.name = validateName($el.value, 'person')"
                class="w-full px-4 py-3 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white rounded-lg transition-all focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 dark:focus:ring-indigo-900 placeholder-gray-500 dark:placeholder-gray-400 @error('name') border-red-500 dark:border-red-500 @enderror"
                value="{{ old('name', $client && $client->type === 'person' ? trim($client->first_name.' '.$client->last_name) : '') }}"
                :required="type === 'person'"
                minlength="3"
                maxlength="50"
                placeholder="{{ __('contact.name_placeholder') }}"
            >
            @error('name')
                <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <!-- Company Name Field (Company only) -->
        <div class="mb-6" x-show="type === 'company'">
            <div class="flex items-center justify-between mb-2">
                <label for="company_name" class="block text-sm font-semibold text-gray-900 dark:text-white">
                    {{ __('contact.company_name') }} <span class="text-red-500">*</span>
                </label>
                <span x-show="validation.company_name" class="text-green-500 text-lg">✓</span>
            </div>
            <input
                type="text"
                id="company_name"
                name="company_name"
                @input="validation.company_name = validateName($el.value, 'company')"
                class="w-full px-4 py-3 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white rounded-lg transition-all focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 dark:focus:ring-indigo-900 placeholder-gray-500 dark:placeholder-gray-400 @error('company_name') border-red-500 dark:border-red-500 @enderror"
                value="{{ old('company_name', $client && $client->type === 'company' ? $client->company_name : '') }}"
                :required="type === 'company'"
                minlength="3"
                maxlength="100"
                placeholder="{{ __('contact.company_placeholder') }}"
            >
            @error('company_name')
                <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <!-- Contact Person Field (Company only) -->
        <div class="mb-6" x-show="type === 'company'">
            <div class="flex items-center justify-between mb-2">
                <label for="contact_name" class="block text-sm font-semibold text-gray-900 dark:text-white">
                    {{ __('contact.contact_person') }}
                </label>
                <span x-show="validation.contact_name" class="text-green-500 text-lg">✓</span>
            </div>
            <input
                type="text"
                id="contact_name"
                name="contact_name"
                @input="validation.contact_name = $el.value.length === 0 || ($el.value.length >= 3 && /^[\p{L}\s\-'0-9]+$/u.test($el.value))"
                class="w-full px-4 py-3 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white rounded-lg transition-all focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 dark:focus:ring-indigo-900 placeholder-gray-500 dark:placeholder-gray-400 @error('contact_name') border-red-500 dark:border-red-500 @enderror"
                value="{{ old('contact_name', $client && $client->type === 'company' ? $client->contact_name : '') }}"
                minlength="3"
                maxlength="100"
                placeholder="{{ __('contact.contact_person_placeholder') }}"
            >
            @error('contact_name')
                <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <!-- Email Field -->
        <div class="mb-6">
            <div class="flex items-center justify-between mb-2">
                <label for="email" class="block text-sm font-semibold text-gray-900 dark:text-white">
                    {{ __('contact.email') }} <span class="text-red-500">*</span>
                </label>
                <span x-show="validation.email" class="text-green-500 text-lg">✓</span>
            </div>
            <input
                type="email"
                id="email"
                name="email"
                @input="validation.email = validateEmail($el.value)"
                class="w-full px-4 py-3 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white rounded-lg transition-all focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 dark:focus:ring-indigo-900 placeholder-gray-500 dark:placeholder-gray-400 @error('email') border-red-500 dark:border-red-500 @enderror"
                value="{{ old('email', $client?->email ?? '') }}"
                :required="true"
                placeholder="{{ __('contact.email_placeholder') }}"
            >
            @error('email')
                <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <!-- Contact Email Checkbox (Company only) -->
        <div class="mb-6" x-show="type === 'company'">
            <label class="flex items-center gap-3 cursor-pointer">
                <input
                    type="checkbox"
                    x-model="useSameEmail"
                    class="w-4 h-4 text-indigo-600 bg-gray-100 border-gray-300 rounded focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:ring-offset-gray-800 dark:bg-gray-700 dark:border-gray-600"
                >
                <span class="text-gray-700 dark:text-gray-300 text-sm">
                    {{ __('contact.use_same_email_as_main') }}
                </span>
            </label>
            <input type="hidden" name="use_same_email" x-bind:value="useSameEmail ? 'on' : ''">
        </div>

        <!-- Contact Email Field (Company only, if not using same email) -->
        <div class="mb-6" x-show="type === 'company' && !useSameEmail">
            <div class="flex items-center justify-between mb-2">
                <label for="contact_email" class="block text-sm font-semibold text-gray-900 dark:text-white">
                    {{ __('contact.contact_email') }}
                </label>
                <span x-show="validation.contact_email" class="text-green-500 text-lg">✓</span>
            </div>
            <input
                type="email"
                id="contact_email"
                name="contact_email"
                @input="validation.contact_email = $el.value.length === 0 || validateEmail($el.value)"
                class="w-full px-4 py-3 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white rounded-lg transition-all focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 dark:focus:ring-indigo-900 placeholder-gray-500 dark:placeholder-gray-400 @error('contact_email') border-red-500 dark:border-red-500 @enderror"
                value="{{ old('contact_email', $client && $client->type === 'company' ? $client->contact_email : '') }}"
                placeholder="{{ __('contact.contact_email_placeholder') }}"
            >
            @error('contact_email')
                <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <!-- Subject Field -->
        <div class="mb-6">
            <div class="flex items-center justify-between mb-2">
                <label for="subject" class="block text-sm font-semibold text-gray-900 dark:text-white">
                    {{ __('contact.subject') }} <span class="text-red-500">*</span>
                </label>
                <span x-show="validation.subject" class="text-green-500 text-lg">✓</span>
            </div>
            <input
                type="text"
                id="subject"
                name="subject"
                @input="validation.subject = validateSubject($el.value)"
                class="w-full px-4 py-3 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white rounded-lg transition-all focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 dark:focus:ring-indigo-900 placeholder-gray-500 dark:placeholder-gray-400 @error('subject') border-red-500 dark:border-red-500 @enderror"
                value="{{ old('subject') }}"
                :required="true"
                minlength="5"
                maxlength="100"
                placeholder="{{ __('contact.subject_placeholder') }}"
            >
            @error('subject')
                <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <!-- Message Field -->
        <div class="mb-6">
            <div class="flex items-center justify-between mb-2">
                <label for="message" class="block text-sm font-semibold text-gray-900 dark:text-white">
                    {{ __('contact.message') }} <span class="text-red-500">*</span>
                </label>
                <span x-show="validation.message" class="text-green-500 text-lg">✓</span>
            </div>
            <textarea
                id="message"
                name="message"
                @input="validation.message = validateMessage($el.value)"
                class="w-full px-4 py-3 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white rounded-lg transition-all focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 dark:focus:ring-indigo-900 placeholder-gray-500 dark:placeholder-gray-400 @error('message') border-red-500 dark:border-red-500 @enderror"
                :required="true"
                minlength="10"
                maxlength="2000"
                rows="6"
                placeholder="{{ __('contact.message_placeholder') }}"
            >{{ old('message') }}</textarea>
            <p class="mt-2 text-xs text-gray-600 dark:text-gray-400">{{ __('contact.message_hint') }}</p>
            @error('message')
                <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <!-- Submit Button -->
        <button type="submit" class="w-full px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-base rounded-lg shadow-lg hover:shadow-xl transition-all duration-200 hover:-translate-y-0.5 cursor-pointer">
            {{ __('contact.send_button') }}
        </button>
    </form>

    <!-- Email Alternative -->
    @if($contactEmail = \App\Models\Setting::get('contact_email'))
        <div class="mt-12 text-center pt-8 border-t border-gray-200 dark:border-gray-700">
            <p class="text-gray-600 dark:text-gray-400 mb-3">
                {{ __('contact.or_email') }}
            </p>
            <a href="mailto:{{ $contactEmail }}" class="inline-flex items-center gap-2 px-6 py-3 border-2 border-indigo-600 dark:border-indigo-500 text-indigo-600 dark:text-indigo-400 font-semibold rounded-lg hover:bg-indigo-600 hover:text-white dark:hover:bg-indigo-500 dark:hover:text-white transition-all duration-200">
                {{ $contactEmail }}
            </a>
        </div>
    @endif
</div>

<style>
    @keyframes slidedown {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes fadeout {
        to {
            opacity: 0;
            transform: translateY(-10px);
        }
    }

    .animate-slidedown {
        animation: slidedown 0.3s ease;
    }

    .alert-fade-out {
        animation: fadeout 0.3s ease forwards;
    }

    button[type="submit"] {
        background-color: #4f46e5 !important;
        color: white !important;
    }

    button[type="submit"]:hover {
        background-color: #4338ca !important;
        box-shadow: 0 10px 15px -3px rgba(79, 70, 229, 0.3) !important;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const alerts = document.querySelectorAll('[class*="border-l-4"]');
        alerts.forEach(alert => {
            if (alert.classList.contains('animate-slidedown')) {
                setTimeout(() => {
                    alert.classList.add('alert-fade-out');
                    setTimeout(() => alert.remove(), 300);
                }, 5000);
            }
        });
    });
</script>
@endsection
