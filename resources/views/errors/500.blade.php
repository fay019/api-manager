@extends('errors.server')

@section('title', __('errors.500.title'))
@section('code', '500')
@section('message', __('errors.500.message'))

@section('debug')
    <div class="w-full space-y-6 pt-8">
        @if(config('app.debug'))
            <!-- Debug Notice -->
            <div class="mx-auto max-w-2xl rounded-lg border border-red-200 bg-red-50 p-6 dark:border-red-900/30 dark:bg-red-900/10">
                <h3 class="flex items-center gap-2 font-semibold text-red-900 dark:text-red-200">
                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                    </svg>
                    {{ __('errors.500.debug_enabled') }}
                </h3>
                <p class="mt-2 text-sm text-red-800 dark:text-red-300">
                    {{ __('errors.500.enable_debug') }}
                </p>
            </div>

            <!-- Recent Logs -->
            <div class="mx-auto max-w-2xl space-y-3">
                <h3 class="font-semibold text-gray-900 dark:text-white">{{ __('errors.500.recent_logs') }}</h3>
                <div class="max-h-96 overflow-y-auto rounded-lg border border-gray-300 bg-gray-50 p-4 font-mono text-sm text-gray-800 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">
                    @php
                        $logFile = storage_path('logs/laravel.log');
                        if (file_exists($logFile)) {
                            $lines = array_reverse(file($logFile));
                            $displayed = 0;
                            foreach ($lines as $line) {
                                if ($displayed >= 30) break;
                                $trimmed = trim($line);
                                if (!empty($trimmed)) {
                                    if (strpos($trimmed, 'ERROR') !== false || strpos($trimmed, 'Exception') !== false) {
                                        echo '<div class="border-l-2 border-red-500 bg-red-50 px-3 py-1 text-red-700 dark:border-red-400 dark:bg-red-900/20 dark:text-red-300">' . htmlspecialchars($trimmed) . '</div>';
                                    } else {
                                        echo '<div class="border-l-2 border-gray-300 px-3 py-1 dark:border-gray-700">' . htmlspecialchars($trimmed) . '</div>';
                                    }
                                    $displayed++;
                                }
                            }
                        } else {
                            echo '<div class="text-gray-500 dark:text-gray-400">' . __('errors.500.no_logs') . '</div>';
                        }
                    @endphp
                </div>
            </div>

            <!-- Log File Path -->
            <div class="mx-auto max-w-2xl rounded-lg border border-gray-300 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-900">
                <p class="text-xs font-mono text-gray-600 dark:text-gray-400">
                    <strong>{{ __('errors.500.full_log') }}:</strong><br>
                    {{ $logFile ?? storage_path('logs/laravel.log') }}
                </p>
            </div>
        @else
            <div class="mx-auto max-w-md rounded-lg border border-blue-200 bg-blue-50 p-6 dark:border-blue-900/30 dark:bg-blue-900/10">
                <div class="flex items-start gap-3">
                    <svg class="h-5 w-5 flex-shrink-0 text-blue-600 dark:text-blue-400 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 5v8a2 2 0 01-2 2h-5l-5 4v-4H4a2 2 0 01-2-2V5a2 2 0 012-2h12a2 2 0 012 2zm-11-1a1 1 0 11-2 0 1 1 0 012 0zm3 1a1 1 0 100-2 1 1 0 000 2zm3 0a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" />
                    </svg>
                    <div>
                        <h3 class="font-semibold text-blue-900 dark:text-blue-200">{{ __('errors.500.enable_debug') }}</h3>
                        <p class="mt-1 text-sm text-blue-800 dark:text-blue-300">
                            Enable debug mode in your .env file to see detailed error information and logs.
                        </p>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection
