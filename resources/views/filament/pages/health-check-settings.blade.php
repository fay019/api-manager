<x-filament-panels::page>
    <div class="space-y-6">
        <!-- Configuration Toggles -->
        <div style="background: white; border: 1px solid #d1d5db; border-radius: 0.5rem; overflow: hidden;">
            <!-- Header -->
            <div style="background: #f9fafb; padding: 1rem; border-bottom: 1px solid #e5e7eb;">
                <h3 style="font-size: 1.125rem; font-weight: 600; color: #111827; margin: 0;">
                    API Health Check Configuration
                </h3>
                <p style="font-size: 0.875rem; color: #6b7280; margin: 0.25rem 0 0 0;">
                    Configure which checks are performed by GET /api/v1/health
                </p>
            </div>

            <!-- Content -->
            <div style="padding: 1.5rem;">
                @php
                    $settings = \App\Models\HealthCheckSetting::getInstance();
                    $checks = [
                        'cache' => ['label' => 'Cache Check', 'description' => 'Test cache write/read functionality'],
                        'logs' => ['label' => 'Logs Check', 'description' => 'Verify logs directory is writable'],
                        'disk_space' => ['label' => 'Disk Space Check', 'description' => 'Monitor available disk space and show warning if > 90% used'],
                        'storage' => ['label' => 'Storage Check', 'description' => 'Verify all storage directories (logs, app, cache, sessions) are writable'],
                    ];
                @endphp

                <div style="display: grid; gap: 1.5rem;">
                    @foreach($checks as $checkKey => $checkInfo)
                        @php
                            $isEnabled = $settings->{$checkKey . '_enabled'};
                        @endphp
                        <div style="display: flex; align-items: flex-start; justify-content: space-between; padding-bottom: 1.5rem; border-bottom: 1px solid #e5e7eb;" class="last:border-0 last:pb-0">
                            <div style="flex: 1;">
                                <p style="font-weight: 600; color: #111827; margin: 0 0 0.5rem 0;">
                                    {{ $checkInfo['label'] }}
                                </p>
                                <p style="font-size: 0.875rem; color: #6b7280; margin: 0;">
                                    {{ $checkInfo['description'] }}
                                </p>
                            </div>
                            <div style="margin-left: 1rem;">
                                <button
                                    wire:click="toggleCheck('{{ $checkKey }}')"
                                    style="
                                        display: inline-block;
                                        padding: 0.375rem 0.75rem;
                                        border-radius: 0.375rem;
                                        border: 1px solid #d1d5db;
                                        background: {{ $isEnabled ? '#dcfce7' : '#f3f4f6' }};
                                        color: {{ $isEnabled ? '#15803d' : '#6b7280' }};
                                        font-weight: 500;
                                        cursor: pointer;
                                        font-size: 0.875rem;
                                        transition: all 0.2s;
                                    "
                                    onmouseover="this.style.opacity='0.8'"
                                    onmouseout="this.style.opacity='1'"
                                >
                                    {{ $isEnabled ? '✓ Enabled' : '○ Disabled' }}
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
                Test Health Check
            </x-filament::button>
        </div>

        <!-- Health Check Result Preview -->
        @if(session('health_check_result'))
            <div style="background: white; border: 1px solid #d1d5db; border-radius: 0.5rem; overflow: hidden;">
                <!-- Header -->
                <div style="background: #f9fafb; padding: 1rem; border-bottom: 1px solid #e5e7eb;">
                    <h3 style="font-size: 1.125rem; font-weight: 600; color: #111827; margin: 0;">
                        🔍 Health Check Result
                    </h3>
                    <p style="font-size: 0.875rem; color: #6b7280; margin: 0.25rem 0 0 0;">
                        Last test executed at {{ now()->format('H:i:s') }}
                    </p>
                </div>

                <!-- Content -->
                <div style="padding: 1.5rem;">
                    <!-- Status Indicator -->
                    @php
                        $status = session('health_check_result')['data']['status'] ?? 'unknown';
                        $statusColor = $status === 'ok' ? '#10b981' : '#ef4444';
                        $statusBg = $status === 'ok' ? '#d1fae5' : '#fee2e2';
                    @endphp
                    <div style="margin-bottom: 1.5rem; padding: 1rem; background: {{ $statusBg }}; border-radius: 0.5rem; border-left: 4px solid {{ $statusColor }};">
                        <p style="color: {{ $statusColor }}; font-weight: 600; margin: 0;">
                            Overall Status: <span style="text-transform: uppercase;">{{ $status }}</span>
                        </p>
                    </div>

                    <!-- Checks Status -->
                    @if(isset(session('health_check_result')['data']['checks']))
                        <div style="margin-bottom: 1.5rem;">
                            <h4 style="font-size: 0.875rem; font-weight: 600; color: #111827; margin-bottom: 1rem;">Individual Checks:</h4>
                            <div style="display: grid; gap: 1rem;">
                                @foreach(session('health_check_result')['data']['checks'] as $checkName => $checkResult)
                                    @php
                                        $checkStatus = $checkResult['status'] ?? 'unknown';
                                        $checkColor = match($checkStatus) {
                                            'ok' => '#10b981',
                                            'warning' => '#f59e0b',
                                            default => '#ef4444',
                                        };
                                        $checkBg = match($checkStatus) {
                                            'ok' => '#d1fae5',
                                            'warning' => '#fef3c7',
                                            default => '#fee2e2',
                                        };
                                        $checkIcon = match($checkStatus) {
                                            'ok' => '✓',
                                            'warning' => '⚠',
                                            default => '✕',
                                        };
                                    @endphp
                                    <div style="padding: 1rem; background: {{ $checkBg }}; border-radius: 0.5rem; border-left: 3px solid {{ $checkColor }};">
                                        <div style="display: flex; align-items: flex-start; gap: 0.75rem;">
                                            <span style="color: {{ $checkColor }}; font-weight: bold; font-size: 1.25rem;">{{ $checkIcon }}</span>
                                            <div style="flex: 1;">
                                                <p style="font-weight: 600; color: {{ $checkColor }}; margin: 0; text-transform: capitalize;">
                                                    {{ str_replace('_', ' ', $checkName) }}
                                                </p>
                                                <p style="font-size: 0.875rem; color: {{ $checkColor }}; margin: 0.25rem 0 0 0;">
                                                    {{ $checkResult['message'] ?? 'No message' }}
                                                </p>
                                                @if(isset($checkResult['details']))
                                                    <div style="font-size: 0.8rem; color: {{ $checkColor }}; margin-top: 0.5rem; opacity: 0.8;">
                                                        @foreach($checkResult['details'] as $key => $value)
                                                            <div>{{ $key }}: <strong>{{ $value }}</strong></div>
                                                        @endforeach
                                                    </div>
                                                @endif
                                                @if(isset($checkResult['free_gb']))
                                                    <div style="font-size: 0.8rem; color: {{ $checkColor }}; margin-top: 0.5rem; opacity: 0.8;">
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
                        <h4 style="font-size: 0.875rem; font-weight: 600; color: #111827; margin-bottom: 0.75rem;">Raw JSON Response:</h4>
                        <pre style="background: #1f2937; color: #d1d5db; padding: 1rem; border-radius: 0.5rem; overflow-x: auto; font-size: 0.75rem; line-height: 1.4; margin: 0;"><code>{{ json_encode(session('health_check_result'), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</code></pre>
                    </div>
                </div>
            </div>
        @endif

        <!-- API Endpoint Info -->
        <div style="background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 0.5rem; padding: 1.5rem;">
            <h4 style="font-size: 0.875rem; font-weight: 600; color: #1e40af; margin: 0 0 0.75rem 0;">
                📍 Endpoint Information
            </h4>
            <div style="font-size: 0.875rem; color: #1e40af; line-height: 1.6;">
                <p style="margin: 0 0 0.5rem 0;">
                    <strong>URL:</strong> <code style="background: white; padding: 0.25rem 0.5rem; border-radius: 0.25rem;">GET /api/v1/health</code>
                </p>
                <p style="margin: 0 0 0.5rem 0;">
                    <strong>Authentication:</strong> Not required
                </p>
                <p style="margin: 0;">
                    <strong>Response:</strong> JSON with status and configured health checks
                </p>
            </div>
        </div>
    </div>
</x-filament-panels::page>
