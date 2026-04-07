<style>
    :root {
        --stats-text-dark: #1f2937;
        --stats-text-muted: #6b7280;
        --stats-bg: #f3f4f6;
        --stats-blue-gradient: linear-gradient(135deg, #3b82f6 0%, #1e40af 100%);
        --stats-green-gradient: linear-gradient(135deg, #10b981 0%, #059669 100%);
        --stats-red-gradient: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        --stats-orange-gradient: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        --stats-purple: #8b5cf6;
    }

    html.dark {
        --stats-text-dark: #f3f4f6;
        --stats-text-muted: #d1d5db;
        --stats-bg: #1f2937;
        --stats-blue-gradient: linear-gradient(135deg, #3b82f6 0%, #1e3a8a 100%);
        --stats-green-gradient: linear-gradient(135deg, #10b981 0%, #065f46 100%);
        --stats-red-gradient: linear-gradient(135deg, #ef4444 0%, #b91c1c 100%);
        --stats-orange-gradient: linear-gradient(135deg, #f59e0b 0%, #b45309 100%);
        --stats-purple: #a78bfa;
    }

    .stats-card {
        border-radius: 0.5rem;
        padding: 1rem;
        color: white;
    }

    .stats-card-blue {
        background: var(--stats-blue-gradient);
    }

    .stats-card-green {
        background: var(--stats-green-gradient);
    }

    .stats-card-red {
        background: var(--stats-red-gradient);
    }

    .stats-card-orange {
        background: var(--stats-orange-gradient);
    }

    .stats-card p {
        margin: 0;
    }

    .stats-card-label {
        font-size: 0.875rem;
        opacity: 0.9;
        margin-bottom: 0.5rem;
    }

    .stats-card-value {
        font-size: 2rem;
        font-weight: bold;
    }

    .stats-all-time {
        background: var(--stats-bg);
        border-radius: 0.5rem;
        padding: 1rem;
        border-left: 4px solid var(--stats-purple);
    }

    .stats-all-time-label {
        font-size: 0.875rem;
        color: var(--stats-text-muted);
        margin-bottom: 0.25rem;
    }

    .stats-all-time-value {
        font-size: 1.875rem;
        font-weight: bold;
        color: var(--stats-text-dark);
    }

    .stats-icon {
        font-size: 2.5rem;
    }
</style>

<x-filament-widgets::widget>
    @php
        $stats = $this->getStats();
    @endphp

    <div style="padding: 1.5rem;">
        <h3 style="font-size: 1.125rem; font-weight: 600; margin-bottom: 1rem; color: var(--stats-text-dark);">API Request Statistics</h3>

        <!-- Today Stats Grid -->
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 1rem; margin-bottom: 1.5rem;">
            <!-- Today Total -->
            <div class="stats-card stats-card-blue">
                <p class="stats-card-label">Today Requests</p>
                <p class="stats-card-value">{{ $stats['today_total'] }}</p>
            </div>

            <!-- Success Rate -->
            <div class="stats-card stats-card-green">
                <p class="stats-card-label">Success Rate</p>
                <p class="stats-card-value">{{ $stats['success_rate'] }}%</p>
            </div>

            <!-- Errors Today -->
            <div class="stats-card stats-card-red">
                <p class="stats-card-label">Errors Today</p>
                <p class="stats-card-value">{{ $stats['today_errors'] }}</p>
            </div>

            <!-- Avg Duration -->
            <div class="stats-card stats-card-orange">
                <p class="stats-card-label">Avg Duration</p>
                <p class="stats-card-value">{{ $stats['avg_duration'] }}ms</p>
            </div>
        </div>

        <!-- All Time -->
        <div class="stats-all-time">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <p class="stats-all-time-label">All Time Requests</p>
                    <p class="stats-all-time-value">{{ $stats['all_time_total'] }}</p>
                </div>
                <span class="stats-icon">📊</span>
            </div>
        </div>
    </div>
</x-filament-widgets::widget>
