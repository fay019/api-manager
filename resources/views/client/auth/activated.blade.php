@extends('layouts.app')

@section('content')
<div class="min-h-screen flex items-center justify-center px-4 py-12">
    <div class="w-full max-w-md">
        <div class="bg-white dark:bg-slate-800 rounded-lg shadow-lg p-8 text-center">
            <div class="mb-6">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-green-100 dark:bg-green-900 rounded-full">
                    <svg class="w-8 h-8 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
            </div>

            <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">
                {{ __('client.client_auth.activation_success') }}
            </h1>

            <p class="text-gray-600 dark:text-gray-400 mb-8">
                {{ __('client.you_can_now_login') }}
            </p>

            <a
                href="{{ route('client.login') }}"
                class="inline-block bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-6 rounded-lg transition-colors"
            >
                {{ __('client.client_auth.login_button') }}
            </a>
        </div>
    </div>
</div>
@endsection
