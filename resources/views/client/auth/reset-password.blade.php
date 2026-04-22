@extends('layouts.app')

@section('content')
<div class="min-h-screen flex items-center justify-center px-4 py-12">
    <div class="w-full max-w-md">
        <div class="bg-white dark:bg-slate-800 rounded-lg shadow-lg p-8">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-6 text-center">
                {{ __('client.client_auth.password_reset_title') }}
            </h1>

            @if ($errors->any())
                <div class="mb-6 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach ($errors->all() as $error)
                            <li class="text-red-700 dark:text-red-300 text-sm">{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ url('/client/password/reset') }}" class="space-y-4">
                @csrf

                <input type="hidden" name="token" value="{{ $token }}">
                <input type="hidden" name="email" value="{{ $email }}">

                <div x-data="{ showPwd: false }">
                    <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        {{ __('client.password') }}
                    </label>
                    <div class="relative">
                        <input
                            :type="showPwd ? 'text' : 'password'"
                            id="password"
                            name="password"
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-slate-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 pr-10"
                            required
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
                    @error('password')
                        <p class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div x-data="{ showPwd: false }">
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        {{ __('client.password_confirmation') }}
                    </label>
                    <div class="relative">
                        <input
                            :type="showPwd ? 'text' : 'password'"
                            id="password_confirmation"
                            name="password_confirmation"
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-slate-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 pr-10"
                            required
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
                    @error('password_confirmation')
                        <p class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <button
                    type="submit"
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg transition-colors"
                >
                    {{ __('client.client_auth.password_reset_button') }}
                </button>
            </form>

            <p class="mt-6 text-center text-gray-600 dark:text-gray-400">
                {{ __('client.client_auth.have_account') }}
                <a href="{{ route('client.login') }}" class="text-blue-600 dark:text-blue-400 hover:underline">
                    {{ __('client.client_auth.login_link') }}
                </a>
            </p>
        </div>
    </div>
</div>
@endsection
