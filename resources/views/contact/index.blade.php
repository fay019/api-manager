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

    <!-- Contact Form -->
    <form action="{{ route('contact.store') }}" method="POST" class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-8 sm:p-10 shadow-sm">
        @csrf

        <!-- Honeypot field (hidden from users) -->
        <input type="text" name="website" class="hidden" tabindex="-1" autocomplete="off">

        <!-- Timestamp field (hidden) -->
        <input type="hidden" name="form_timestamp" value="{{ time() }}">

        <!-- Language field (hidden) -->
        <input type="hidden" name="language" value="{{ app()->getLocale() }}">

        <!-- Name Field -->
        <div class="mb-6">
            <label for="name" class="block text-sm font-semibold text-gray-900 dark:text-white mb-2">
                {{ __('contact.name') }} <span class="text-red-500">*</span>
            </label>
            <input
                type="text"
                id="name"
                name="name"
                class="w-full px-4 py-3 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white rounded-lg transition-all focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 dark:focus:ring-indigo-900 placeholder-gray-500 dark:placeholder-gray-400 @error('name') border-red-500 dark:border-red-500 @enderror"
                value="{{ old('name') }}"
                required
                minlength="3"
                maxlength="50"
                placeholder="{{ __('contact.name_placeholder') }}"
            >
            @error('name')
                <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <!-- Email Field -->
        <div class="mb-6">
            <label for="email" class="block text-sm font-semibold text-gray-900 dark:text-white mb-2">
                {{ __('contact.email') }} <span class="text-red-500">*</span>
            </label>
            <input
                type="email"
                id="email"
                name="email"
                class="w-full px-4 py-3 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white rounded-lg transition-all focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 dark:focus:ring-indigo-900 placeholder-gray-500 dark:placeholder-gray-400 @error('email') border-red-500 dark:border-red-500 @enderror"
                value="{{ old('email') }}"
                required
                placeholder="{{ __('contact.email_placeholder') }}"
            >
            @error('email')
                <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <!-- Subject Field -->
        <div class="mb-6">
            <label for="subject" class="block text-sm font-semibold text-gray-900 dark:text-white mb-2">
                {{ __('contact.subject') }} <span class="text-red-500">*</span>
            </label>
            <input
                type="text"
                id="subject"
                name="subject"
                class="w-full px-4 py-3 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white rounded-lg transition-all focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 dark:focus:ring-indigo-900 placeholder-gray-500 dark:placeholder-gray-400 @error('subject') border-red-500 dark:border-red-500 @enderror"
                value="{{ old('subject') }}"
                required
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
            <label for="message" class="block text-sm font-semibold text-gray-900 dark:text-white mb-2">
                {{ __('contact.message') }} <span class="text-red-500">*</span>
            </label>
            <textarea
                id="message"
                name="message"
                class="w-full px-4 py-3 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white rounded-lg transition-all focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 dark:focus:ring-indigo-900 placeholder-gray-500 dark:placeholder-gray-400 @error('message') border-red-500 dark:border-red-500 @enderror"
                required
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
