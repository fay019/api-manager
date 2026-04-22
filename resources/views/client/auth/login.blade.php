@extends('layouts.app')

@section('content')
<div class="min-h-screen flex items-center justify-center px-4 py-12">
    <div class="w-full max-w-md">
        <div class="bg-white dark:bg-slate-800 rounded-lg shadow-lg p-8">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-6 text-center">
                {{ __('client.client_auth.login_title') }}
            </h1>

            @if ($errors->any())
                <div class="mb-6 p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-900 rounded-lg">
                    <ul class="list-disc list-inside text-sm text-red-600 dark:text-red-400">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if ($message = session('success'))
                <div class="mb-6 p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-900 rounded-lg">
                    <p class="text-sm text-green-600 dark:text-green-400">{{ $message }}</p>
                </div>
            @endif

            <form method="POST" action="{{ route('client.login') }}" class="space-y-6">
                @csrf

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        {{ __('client.client.email') }}
                    </label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        value="{{ old('email') }}"
                        required
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-slate-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
                    >
                </div>

                <div x-data="{ showPwd: false }">
                    <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        {{ __('client.password') }}
                    </label>
                    <div class="relative">
                        <input
                            :type="showPwd ? 'text' : 'password'"
                            id="password"
                            name="password"
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
                </div>

                <button
                    type="submit"
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg transition-colors"
                >
                    {{ __('client.client_auth.login_button') }}
                </button>
            </form>

            <div class="mt-6 space-y-4 text-center">
                <p>
                    <a href="{{ route('client.password.forgot') }}" class="text-sm text-blue-600 hover:text-blue-700 font-semibold">
                        {{ __('client.client_auth.password_forgot_link') }}
                    </a>
                </p>

                <p class="text-gray-600 dark:text-gray-400">
                    {{ __('client.client_auth.no_account') }}
                    <a href="{{ route('client.register') }}" class="text-blue-600 hover:text-blue-700 font-semibold">
                        {{ __('client.client_auth.register_link') }}
                    </a>
                </p>

                <form method="POST" action="{{ route('client.activate.resend') }}" class="inline">
                    @csrf
                    <input type="hidden" name="email" value="{{ session('resend_email', old('email')) }}">
                    <button
                        type="submit"
                        class="text-sm text-blue-600 hover:text-blue-700 font-semibold"
                    >
                        {{ __('client.client_auth.resend_link') }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
