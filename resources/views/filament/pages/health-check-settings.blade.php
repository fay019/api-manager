<style>
    :root {
        --hc-text-dark: #111827;
        --hc-text-muted: #6b7280;
        --hc-bg-card: white;
        --hc-bg-header: #f9fafb;
        --hc-border: #d1d5db;
        --hc-border-light: #e5e7eb;
        --hc-green: #10b981;
        --hc-green-light: #d1fae5;
        --hc-red: #ef4444;
        --hc-red-light: #fee2e2;
        --hc-orange: #f59e0b;
        --hc-orange-light: #fef3c7;
        --hc-blue: #3b82f6;
        --hc-blue-bg: #eff6ff;
        --hc-blue-dark: #1e40af;
        --hc-json-bg: #1f2937;
        --hc-json-text: #d1d5db;
        --hc-disabled-bg: #f3f4f6;
        --hc-disabled-text: #6b7280;
        --hc-enabled-text: #15803d;
    }

    html.dark {
        --hc-text-dark: #f3f4f6;
        --hc-text-muted: #d1d5db;
        --hc-bg-card: #1f2937;
        --hc-bg-header: #111827;
        --hc-border: #374151;
        --hc-border-light: #374151;
        --hc-green: #10b981;
        --hc-green-light: #064e3b;
        --hc-red: #ef4444;
        --hc-red-light: #7f1d1d;
        --hc-orange: #f59e0b;
        --hc-orange-light: #78350f;
        --hc-blue: #3b82f6;
        --hc-blue-bg: #1e3a8a;
        --hc-blue-dark: #93c5fd;
        --hc-json-bg: #0f172a;
        --hc-json-text: #e2e8f0;
        --hc-disabled-bg: #374151;
        --hc-disabled-text: #9ca3af;
        --hc-enabled-text: #86efac;
    }

    .hc-card {
        background: var(--hc-bg-card);
        border: 1px solid var(--hc-border);
        border-radius: 0.5rem;
        overflow: hidden;
    }

    .hc-header {
        background: var(--hc-bg-header);
        padding: 1rem;
        border-bottom: 1px solid var(--hc-border-light);
    }

    .hc-header h3 {
        font-size: 1.125rem;
        font-weight: 600;
        color: var(--hc-text-dark);
        margin: 0;
    }

    .hc-header p {
        font-size: 0.875rem;
        color: var(--hc-text-muted);
        margin: 0.25rem 0 0 0;
    }

    .hc-content {
        padding: 1.5rem;
    }

    .hc-divider {
        padding-bottom: 1.5rem;
        border-bottom: 1px solid var(--hc-border-light);
    }

    .hc-divider:last-child {
        border-bottom: none;
        padding-bottom: 0;
    }

    .hc-check-label {
        font-weight: 600;
        color: var(--hc-text-dark);
        margin: 0 0 0.5rem 0;
    }

    .hc-check-desc {
        font-size: 0.875rem;
        color: var(--hc-text-muted);
        margin: 0;
    }

    .hc-toggle-btn {
        display: inline-block;
        padding: 0.375rem 0.75rem;
        border-radius: 0.375rem;
        border: 1px solid var(--hc-border);
        font-weight: 500;
        cursor: pointer;
        font-size: 0.875rem;
        transition: all 0.2s;
    }

    .hc-toggle-enabled {
        background: var(--hc-green-light);
        color: var(--hc-enabled-text);
    }

    .hc-toggle-disabled {
        background: var(--hc-disabled-bg);
        color: var(--hc-disabled-text);
    }

    .hc-status-box {
        margin-bottom: 1.5rem;
        padding: 1rem;
        border-radius: 0.5rem;
        border-left: 4px solid;
    }

    .hc-status-ok {
        background: var(--hc-green-light);
        border-left-color: var(--hc-green);
        color: var(--hc-green);
    }

    .hc-status-warning {
        background: var(--hc-orange-light);
        border-left-color: var(--hc-orange);
        color: var(--hc-orange);
    }

    .hc-status-error {
        background: var(--hc-red-light);
        border-left-color: var(--hc-red);
        color: var(--hc-red);
    }

    .hc-check-item {
        padding: 1rem;
        border-radius: 0.5rem;
        border-left: 3px solid;
    }

    .hc-info-box {
        background: var(--hc-blue-bg);
        border: 1px solid var(--hc-blue);
        border-radius: 0.5rem;
        padding: 1.5rem;
        color: var(--hc-blue-dark);
    }

    .hc-info-box h4 {
        font-size: 0.875rem;
        font-weight: 600;
        color: var(--hc-blue-dark);
        margin: 0 0 0.75rem 0;
    }

    .hc-info-box p {
        font-size: 0.875rem;
        color: var(--hc-blue-dark);
        line-height: 1.6;
        margin: 0 0 0.5rem 0;
    }

    .hc-json-pre {
        background: var(--hc-json-bg);
        color: var(--hc-json-text);
        padding: 1rem;
        border-radius: 0.5rem;
        overflow-x: auto;
        font-size: 0.75rem;
        line-height: 1.4;
        margin: 0;
    }

    .hc-code {
        background: var(--hc-bg-header);
        padding: 0.25rem 0.5rem;
        border-radius: 0.25rem;
    }
</style>

<x-filament-panels::page>
    <div class="space-y-6">
        <!-- Configuration Toggles -->
        <div class="hc-card">
            <!-- Header -->
            <div class="hc-header">
                <h3>{{ __('filament.health.page_header') }}</h3>
                <p>{{ __('filament.health.page_description') }}</p>
            </div>

            <!-- Content -->
            <div class="hc-content">
                @php
                    $settings = \App\Models\HealthCheckSetting::getInstance();
                    $checks = [
                        'cache' => ['label' => __('filament.health.cache_check'), 'description' => __('filament.health.cache_check_desc')],
                        'logs' => ['label' => __('filament.health.logs_check'), 'description' => __('filament.health.logs_check_desc')],
                        'disk_space' => ['label' => __('filament.health.disk_space_check'), 'description' => __('filament.health.disk_space_check_desc')],
                        'storage' => ['label' => __('filament.health.storage_check'), 'description' => __('filament.health.storage_check_desc')],
                    ];
                @endphp

                <div style="display: grid; gap: 1.5rem;">
                    @foreach($checks as $checkKey => $checkInfo)
                        @php
                            $isEnabled = $settings->{$checkKey . '_enabled'};
                        @endphp
                        <div style="display: flex; align-items: flex-start; justify-content: space-between;" class="hc-divider">
                            <div style="flex: 1;">
                                <p class="hc-check-label">{{ $checkInfo['label'] }}</p>
                                <p class="hc-check-desc">{{ $checkInfo['description'] }}</p>
                            </div>
                            <div style="margin-left: 1rem;">
                                <button
                                    wire:click="toggleCheck('{{ $checkKey }}')"
                                    class="hc-toggle-btn {{ $isEnabled ? 'hc-toggle-enabled' : 'hc-toggle-disabled' }}"
                                    onmouseover="this.style.opacity='0.8'"
                                    onmouseout="this.style.opacity='1'"
                                >
                                    {{ $isEnabled ? __('filament.health.enabled') : __('filament.health.disabled') }}
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div style="display: flex; gap: 0.75rem; flex-wrap: wrap; margin: 2rem 0;">
            <x-filament::button
                wire:click="testHealthCheck"
                color="info"
                icon="heroicon-m-play"
            >
                {{ __('filament.health.test_button') }}
            </x-filament::button>
        </div>

        <!-- Health Check Result Preview -->
        @if(session('health_check_result'))
            <div class="hc-card">
                <!-- Header -->
                <div class="hc-header">
                    <h3>{{ __('filament.health.result_header') }}</h3>
                    <p>{{ __('filament.health.result_executed') }} {{ now()->format('H:i:s') }}</p>
                </div>

                <!-- Content -->
                <div class="hc-content">
                    <!-- Status Indicator -->
                    @php
                        $status = session('health_check_result')['data']['status'] ?? 'unknown';
                        $statusClass = $status === 'ok' ? 'hc-status-ok' : 'hc-status-error';
                    @endphp
                    <div class="hc-status-box {{ $statusClass }}">
                        <p style="font-weight: 600; margin: 0;">
                            {{ __('filament.health.overall_status') }} <span style="text-transform: uppercase;">{{ $status }}</span>
                        </p>
                    </div>

                    <!-- Checks Status -->
                    @if(isset(session('health_check_result')['data']['checks']))
                        <div style="margin-bottom: 1.5rem;">
                            <h4 style="font-size: 0.875rem; font-weight: 600; color: var(--hc-text-dark); margin-bottom: 1rem;">{{ __('filament.health.individual_checks') }}</h4>
                            <div style="display: grid; gap: 1rem;">
                                @foreach(session('health_check_result')['data']['checks'] as $checkName => $checkResult)
                                    @php
                                        $checkStatus = $checkResult['status'] ?? 'unknown';
                                        $checkClass = match($checkStatus) {
                                            'ok' => 'hc-status-ok',
                                            'warning' => 'hc-status-warning',
                                            default => 'hc-status-error',
                                        };
                                        $checkIcon = match($checkStatus) {
                                            'ok' => '✓',
                                            'warning' => '⚠',
                                            default => '✕',
                                        };
                                    @endphp
                                    <div class="hc-check-item {{ $checkClass }}">
                                        <div style="display: flex; align-items: flex-start; gap: 0.75rem;">
                                            <span style="font-weight: bold; font-size: 1.25rem;">{{ $checkIcon }}</span>
                                            <div style="flex: 1;">
                                                <p style="font-weight: 600; margin: 0; text-transform: capitalize;">
                                                    {{ str_replace('_', ' ', $checkName) }}
                                                </p>
                                                <p style="font-size: 0.875rem; margin: 0.25rem 0 0 0;">
                                                    {{ $checkResult['message'] ?? 'No message' }}
                                                </p>
                                                @if(isset($checkResult['details']))
                                                    <div style="font-size: 0.8rem; margin-top: 0.5rem; opacity: 0.8;">
                                                        @foreach($checkResult['details'] as $key => $value)
                                                            <div>{{ $key }}: <strong>{{ $value }}</strong></div>
                                                        @endforeach
                                                    </div>
                                                @endif
                                                @if(isset($checkResult['free_gb']))
                                                    <div style="font-size: 0.8rem; margin-top: 0.5rem; opacity: 0.8;">
                                                        <div>Free: <strong>{{ $checkResult['free_gb'] }} GB</strong></div>
                                                        <div>Total: <strong>{{ $checkResult['total_gb'] }} GB</strong></div>
                                                        <div>Used: <strong>{{ $checkResult['percent_used'] }}%</strong></div>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- JSON Preview -->
                    <div>
                        <h4 style="font-size: 0.875rem; font-weight: 600; color: var(--hc-text-dark); margin-bottom: 0.75rem;">{{ __('filament.health.raw_json') }}</h4>
                        <pre class="hc-json-pre"><code>{{ json_encode(session('health_check_result'), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</code></pre>
                    </div>
                </div>
            </div>
        @endif

        <!-- API Endpoint Info -->
        <div class="hc-info-box">
            <h4>{{ __('filament.health.endpoint_info') }}</h4>
            <p>
                <strong>{{ __('filament.health.endpoint_url') }}</strong> <code class="hc-code">GET /api/v1/health</code>
            </p>
            <p>
                <strong>{{ __('filament.health.endpoint_auth') }}</strong> {{ __('filament.health.endpoint_auth_not_required') }}
            </p>
            <p>
                <strong>{{ __('filament.health.endpoint_response') }}</strong> {{ __('filament.health.endpoint_response_desc') }}
            </p>
        </div>
    </div>
</x-filament-panels::page>
