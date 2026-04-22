@extends('layouts.app')

@section('content')
<div class="min-h-screen px-4 py-12">
    <div class="max-w-2xl mx-auto">
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
                {{ __('client.client_auth.profile_title') }}
            </h1>
            <a href="{{ route('client.dashboard') }}" class="text-blue-600 hover:text-blue-700 font-semibold">
                ← {{ __('client.back') }}
            </a>
        </div>

        @if (session('success'))
            <div class="mb-6 p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-900 rounded-lg">
                <p class="text-sm text-green-600 dark:text-green-400">{{ session('success') }}</p>
            </div>
        @endif

        <div class="bg-white dark:bg-slate-800 rounded-lg shadow-lg p-8">
            <form method="POST" action="{{ route('client.profile.update') }}" enctype="multipart/form-data" class="space-y-8">
                @csrf
                @method('PUT')

                <!-- Contact Information -->
                <fieldset class="border border-gray-200 dark:border-gray-700 rounded-lg p-6">
                    <legend class="text-lg font-semibold text-gray-900 dark:text-white px-2">
                        {{ __('client.contact') }}
                    </legend>

                    <div class="mt-4 space-y-6">
                        <div>
                            <label for="contact_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                {{ __('client.client.contact_name') }}
                            </label>
                            <input
                                type="text"
                                id="contact_name"
                                name="contact_name"
                                value="{{ old('contact_name', $client->contact_name) }}"
                                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-slate-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
                            >
                            @error('contact_name')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="contact_email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                {{ __('client.client.contact_email') }}
                            </label>
                            <input
                                type="email"
                                id="contact_email"
                                name="contact_email"
                                value="{{ old('contact_email', $client->contact_email) }}"
                                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-slate-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
                            >
                            @error('contact_email')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                {{ __('client.client.description') }}
                            </label>
                            <textarea
                                id="description"
                                name="description"
                                rows="4"
                                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-slate-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
                            >{{ old('description', $client->description) }}</textarea>
                            @error('description')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </fieldset>

                <!-- Avatar -->
                <fieldset class="border border-gray-200 dark:border-gray-700 rounded-lg p-6">
                    <legend class="text-lg font-semibold text-gray-900 dark:text-white px-2">
                        {{ __('client.client.avatar') }}
                    </legend>

                    <div class="mt-4 space-y-4">
                        @if ($client->avatar)
                            <div class="flex items-center gap-4">
                                <img src="{{ Storage::url($client->avatar) }}" alt="{{ $client->name }}" class="w-16 h-16 rounded-lg object-cover">
                                <div>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">
                                        {{ __('client.current_avatar') }}
                                    </p>
                                </div>
                            </div>
                        @endif

                        <div>
                            <label for="avatar" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                {{ __('client.upload_avatar') }}
                            </label>
                            <input
                                type="file"
                                id="avatar"
                                name="avatar"
                                accept="image/*"
                                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-slate-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
                            >
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                {{ __('client.max_file_size', ['size' => '2 MB']) }}
                            </p>
                            @error('avatar')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </fieldset>

                <!-- Password -->
                <fieldset class="border border-gray-200 dark:border-gray-700 rounded-lg p-6">
                    <legend class="text-lg font-semibold text-gray-900 dark:text-white px-2">
                        {{ __('client.client_auth.password_change') }}
                    </legend>

                    <div class="mt-4 space-y-6">
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            {{ __('client.client_auth.password_hint') }}
                        </p>

                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                {{ __('client.password') }}
                            </label>
                            <input
                                type="password"
                                id="password"
                                name="password"
                                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-slate-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
                            >
                            @error('password')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="password_confirmation" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                {{ __('client.password_confirmation') }}
                            </label>
                            <input
                                type="password"
                                id="password_confirmation"
                                name="password_confirmation"
                                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-slate-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
                            >
                        </div>
                    </div>
                </fieldset>

                <!-- Submit -->
                <div class="flex gap-4">
                    <button
                        type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-6 rounded-lg transition-colors"
                    >
                        {{ __('client.save') }}
                    </button>
                    <a
                        href="{{ route('client.dashboard') }}"
                        class="bg-gray-300 dark:bg-gray-700 hover:bg-gray-400 dark:hover:bg-gray-600 text-gray-900 dark:text-white font-semibold py-2 px-6 rounded-lg transition-colors"
                    >
                        {{ __('client.cancel') }}
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
