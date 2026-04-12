@extends('layouts.app')

@section('content')
<div class="flex min-h-[60vh] items-center justify-center px-4 py-16 sm:px-6 lg:px-8">
    <div class="w-full max-w-2xl space-y-8 text-center">
        <!-- Error Code -->
        <div class="space-y-4">
            <div class="text-8xl font-black tracking-tight text-indigo-600 dark:text-indigo-500">
                @yield('code')
            </div>
            <h1 class="text-4xl font-bold text-gray-900 dark:text-white">
                @yield('title')
            </h1>
            <p class="text-xl text-gray-600 dark:text-gray-400">
                @yield('message')
            </p>
        </div>

        <!-- Additional Help Text -->
        @yield('help', '')

        <!-- Actions -->
        <div class="space-y-3 pt-8 sm:flex sm:flex-row sm:gap-4 sm:space-y-0 sm:justify-center">
            <a href="{{ route('home') }}" class="inline-flex w-full items-center justify-center gap-2 rounded-lg bg-indigo-600 px-8 py-4 font-semibold text-white transition-all hover:bg-indigo-700 dark:bg-indigo-700 dark:hover:bg-indigo-600 sm:w-auto">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12a9 9 0 0110-8.9M13.027 12a9 9 0 11-18-4" />
                </svg>
                {{ __('errors.' . ($exception->getStatusCode() ?? '400') . '.back_home') }}
            </a>
            @auth
                <a href="{{ route('profile.edit') }}" class="inline-flex w-full items-center justify-center gap-2 rounded-lg border border-gray-300 px-8 py-4 font-semibold text-gray-700 transition-colors hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-900 sm:w-auto">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    {{ __('auth.my_profile') }}
                </a>
            @else
                <a href="{{ route('login.show') }}" class="inline-flex w-full items-center justify-center gap-2 rounded-lg border border-gray-300 px-8 py-4 font-semibold text-gray-700 transition-colors hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-900 sm:w-auto">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                    </svg>
                    {{ __('auth.login') }}
                </a>
            @endauth
        </div>
    </div>
</div>
@endsection
