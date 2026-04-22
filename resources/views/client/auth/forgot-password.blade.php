@extends('layouts.app')

@section('content')
<div class="min-h-screen flex items-center justify-center px-4 py-12">
    <div class="w-full max-w-md">
        <div class="bg-white dark:bg-slate-800 rounded-lg shadow-lg p-8">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-6 text-center">
                {{ __('client.client_auth.password_forgot_title') }}
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

            <form method="POST" action="{{ route('client.password.forgot') }}" class="space-y-4">
                @csrf

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        {{ __('client.client_auth.password_forgot_description') }}
                    </label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        value="{{ old('email') }}"
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-slate-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
                        required
                    >
                    @error('email')
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
