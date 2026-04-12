@extends('layouts.app')

@section('title', __('auth.my_profile'))

@section('content')
<div class="mx-auto max-w-4xl px-4 py-16 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="mb-12">
        <h1 class="text-4xl font-bold text-gray-900 dark:text-white">{{ __('auth.my_profile') }}</h1>
        <p class="mt-2 text-lg text-gray-600 dark:text-gray-400">{{ __('auth.profile_description') }}</p>
    </div>

    <!-- Success Alert -->
    @if (session('status') === 'profile-updated')
        <div class="mb-8 inline-flex items-center gap-3 rounded-lg border border-green-200 bg-green-50 px-4 py-3 dark:border-green-900/30 dark:bg-green-900/10">
            <svg class="h-5 w-5 text-green-600 dark:text-green-400" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
            </svg>
            <span class="text-sm font-medium text-green-800 dark:text-green-300">{{ __('auth.profile_updated') }}</span>
        </div>
    @endif

    <!-- Main Form Card -->
    <div class="rounded-xl border border-gray-200 bg-white p-8 shadow-sm dark:border-gray-800 dark:bg-gray-900/80 sm:p-12">
        <form action="{{ route('profile.update') }}" method="POST" class="space-y-12">
            @csrf
            @method('PUT')

            <!-- Section: Account Information -->
            <div>
                <div class="mb-8 border-b border-gray-200 pb-8 dark:border-gray-800">
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('auth.account_information') }}</h2>
                </div>

                <div class="grid gap-8 sm:grid-cols-2">
                    <!-- Name Field -->
                    <div>
                        <label for="name" class="block text-sm font-semibold text-gray-900 dark:text-white">
                            {{ __('auth.name') }}
                        </label>
                        <input
                            type="text"
                            id="name"
                            name="name"
                            value="{{ old('name', $user->name) }}"
                            required
                            class="mt-2 block w-full rounded-lg border border-gray-300 bg-white px-4 py-3 text-gray-900 transition-colors placeholder-gray-500 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 dark:border-gray-700 dark:bg-gray-800 dark:text-white dark:placeholder-gray-400 dark:focus:ring-indigo-900 @error('name') border-red-500 dark:border-red-500 @enderror"
                        />
                        @error('name')
                            <p class="mt-2 flex items-center gap-1 text-sm text-red-600 dark:text-red-400">
                                <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                </svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Email Field -->
                    <div>
                        <label for="email" class="block text-sm font-semibold text-gray-900 dark:text-white">
                            {{ __('auth.email') }}
                        </label>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            value="{{ old('email', $user->email) }}"
                            required
                            class="mt-2 block w-full rounded-lg border border-gray-300 bg-white px-4 py-3 text-gray-900 transition-colors placeholder-gray-500 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 dark:border-gray-700 dark:bg-gray-800 dark:text-white dark:placeholder-gray-400 dark:focus:ring-indigo-900 @error('email') border-red-500 dark:border-red-500 @enderror"
                        />
                        @error('email')
                            <p class="mt-2 flex items-center gap-1 text-sm text-red-600 dark:text-red-400">
                                <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                </svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Section: Change Password -->
            <div>
                <div class="mb-8 border-b border-gray-200 pb-8 dark:border-gray-800">
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('auth.change_password') }}</h2>
                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">{{ __('auth.leave_empty') }}</p>
                </div>

                <div class="grid gap-8 sm:grid-cols-2">
                    <!-- Password Field -->
                    <div>
                        <label for="password" class="block text-sm font-semibold text-gray-900 dark:text-white">
                            {{ __('auth.password') }}
                        </label>
                        <input
                            type="password"
                            id="password"
                            name="password"
                            class="mt-2 block w-full rounded-lg border border-gray-300 bg-white px-4 py-3 text-gray-900 transition-colors placeholder-gray-500 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 dark:border-gray-700 dark:bg-gray-800 dark:text-white dark:placeholder-gray-400 dark:focus:ring-indigo-900 @error('password') border-red-500 dark:border-red-500 @enderror"
                        />
                        <p class="mt-2 text-xs text-gray-600 dark:text-gray-400">{{ __('auth.password_hint') }}</p>
                        @error('password')
                            <p class="mt-2 flex items-center gap-1 text-sm text-red-600 dark:text-red-400">
                                <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                </svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Password Confirmation Field -->
                    <div>
                        <label for="password_confirmation" class="block text-sm font-semibold text-gray-900 dark:text-white">
                            {{ __('auth.password_confirmation') }}
                        </label>
                        <input
                            type="password"
                            id="password_confirmation"
                            name="password_confirmation"
                            class="mt-2 block w-full rounded-lg border border-gray-300 bg-white px-4 py-3 text-gray-900 transition-colors placeholder-gray-500 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 dark:border-gray-700 dark:bg-gray-800 dark:text-white dark:placeholder-gray-400 dark:focus:ring-indigo-900 @error('password_confirmation') border-red-500 dark:border-red-500 @enderror"
                        />
                        @error('password_confirmation')
                            <p class="mt-2 flex items-center gap-1 text-sm text-red-600 dark:text-red-400">
                                <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                </svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex flex-col-reverse items-center justify-between gap-4 border-t border-gray-200 pt-8 sm:flex-row dark:border-gray-800">
                <a href="{{ route('home') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-indigo-600 transition-colors hover:text-indigo-700 dark:text-indigo-400 dark:hover:text-indigo-300">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    {{ __('auth.back_home') }}
                </a>
                <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-6 py-3 font-semibold text-white transition-all hover:bg-indigo-700 dark:bg-indigo-700 dark:hover:bg-indigo-600">
                    {{ __('auth.save_changes') }}
                </button>
            </div>
        </form>

        <!-- Logout Section -->
        <div class="mt-12 border-t border-gray-200 pt-12 dark:border-gray-800">
            <h3 class="text-sm font-semibold text-gray-900 dark:text-white">{{ __('auth.logout_section_title') }}</h3>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">{{ __('auth.logout_description') }}</p>
            <form action="{{ route('logout') }}" method="POST" class="mt-4">
                @csrf
                <button type="submit" class="inline-flex items-center gap-2 text-sm font-semibold text-red-600 transition-colors hover:text-red-700 dark:text-red-400 dark:hover:text-red-300">
                    {{ __('auth.logout') }}
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
