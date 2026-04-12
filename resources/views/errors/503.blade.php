@extends('errors.server')

@section('title', __('errors.503.title'))
@section('code', '503')
@section('message', __('errors.503.message'))

@section('debug')
    <div class="w-full space-y-6 pt-8">
        <!-- Maintenance Message & Countdown -->
        @php
            // La langue est déjà gérée par server.blade.php

            // 1. RÉCUPÉRATION DU MESSAGE
            $exceptionMessage = isset($exception) ? $exception->getMessage() : null;
            $reason = env('APP_MAINTENANCE_REASON', 'default');

            $maintenanceMessage = ($exceptionMessage && $exceptionMessage !== 'Service Unavailable')
                ? $exceptionMessage
                : __('errors.maintenance_reasons.' . $reason);

            // 2. GESTION DU RETRY TIME
            $retryTime = null;
            $downFile = storage_path('framework/down');
            if (file_exists($downFile)) {
                $downData = json_decode(file_get_contents($downFile), true);
                if (isset($downData['retry'])) {
                    $retryTime = intval($downData['retry']);
                }
            }

            if (!$retryTime && isset($exception)) {
                $retryAfter = $exception->retryAfter ?? null;
                if ($retryAfter) {
                    if (is_numeric($retryAfter)) {
                        $retryTime = intval($retryAfter);
                    } elseif ($retryAfter instanceof \DateTime) {
                        $retryTime = max(0, (int)($retryAfter->getTimestamp() - time()));
                    }
                }
            }
        @endphp

        <!-- Maintenance Info Box -->
        <div class="mx-auto max-w-md rounded-lg border border-blue-200 bg-blue-50 p-6 dark:border-blue-900/30 dark:bg-blue-900/10">
            <div class="flex items-start gap-3">
                <svg class="h-6 w-6 flex-shrink-0 text-blue-600 dark:text-blue-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                </svg>
                <div class="flex-1">
                    <h3 class="font-semibold text-blue-900 dark:text-blue-200">{{ __('errors.503.debug_title') }}</h3>

                    <!-- Custom Maintenance Message -->
                    @if($maintenanceMessage && $maintenanceMessage !== 'Service Unavailable')
                        <p class="mt-2 text-sm text-blue-800 dark:text-blue-300">
                            {{ $maintenanceMessage }}
                        </p>
                    @else
                        <p class="mt-2 text-sm text-blue-800 dark:text-blue-300">
                            {{ __('errors.503.debug_message') }}
                        </p>
                    @endif

                    <!-- Countdown Timer -->
                    @if($retryTime && $retryTime > 0)
                        <div class="mt-4 pt-3 border-t border-blue-200 dark:border-blue-900/30">
                            <p class="text-xs font-semibold text-blue-600 dark:text-blue-300 uppercase tracking-wide">
                                {{ __('errors.503.back_online') }}
                            </p>
                            <div
                                x-data="countdown({{ $retryTime }})"
                                x-init="init()"
                                class="mt-2 text-lg font-bold text-blue-700 dark:text-blue-200 tabular-nums"
                            >
                                <span x-text="displayTime"></span>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Contact Information (Anti-Bot) -->
        <div class="mx-auto max-w-md rounded-lg border border-gray-300 bg-gray-50 p-5 dark:border-gray-700 dark:bg-gray-900/50">
            <div class="flex items-center gap-2 text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wide">
                <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"></path>
                    <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"></path>
                </svg>
                {{ __('errors.503.need_help') }}
            </div>
            <p class="mt-3 text-sm text-gray-700 dark:text-gray-300">
                {{ __('errors.503.contact_us') }}
            </p>
            <a
                href="#"
                x-data="{ email: '{{ base64_encode('support@' . config('app.name', 'api-manager') . '.local') }}' }"
                @click.prevent="window.location.href = 'mailto:' + atob(email)"
                class="mt-2 inline-flex items-center gap-2 text-sm font-medium text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300 transition-colors"
                title="{{ __('errors.503.click_to_email') }}"
            >
                <span x-text="atob(email)"></span>
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                </svg>
            </a>
        </div>
    </div>

    <script>
    function countdown(seconds) {
        return {
            remaining: seconds,
            displayTime: formatTime(seconds),

            init() {
                if (this.remaining <= 0) return;

                setInterval(() => {
                    this.remaining--;
                    this.displayTime = formatTime(this.remaining);
                }, 1000);
            }
        };

        function formatTime(totalSeconds) {
            if (totalSeconds <= 0) return "{{ __('errors.503.refreshing') }}";

            const minutes = Math.floor(totalSeconds / 60);
            const seconds = totalSeconds % 60;

            if (minutes > 0) {
                return `${minutes}m ${seconds.toString().padStart(2, '0')}s`;
            }
            return `${seconds}s`;
        }
    }
    </script>
@endsection
