<style>
    :root {
        --color-text-dark: #1f2937;
        --color-text-muted: #6b7280;
        --color-text-lighter: #9ca3af;
        --color-bg-card: white;
        --color-bg-header: #f9fafb;
        --color-bg-progress: #e5e7eb;
        --color-border: #e5e7eb;
        --color-blue: #3b82f6;
        --color-green: #10b981;
        --color-orange: #f59e0b;
        --color-red: #ef4444;
        --color-purple: #8b5cf6;
    }

    html.dark {
        --color-text-dark: #f3f4f6;
        --color-text-muted: #d1d5db;
        --color-text-lighter: #9ca3af;
        --color-bg-card: #1f2937;
        --color-bg-header: #111827;
        --color-bg-progress: #374151;
        --color-border: #374151;
    }

    .analytics-container {
        padding: 2rem;
    }

    .analytics-title {
        font-size: 1.875rem;
        font-weight: bold;
        margin-bottom: 1rem;
        color: var(--color-text-dark);
    }

    .analytics-section {
        margin-bottom: 2rem;
    }

    .card {
        background: var(--color-bg-card);
        border-radius: 0.5rem;
        padding: 1.5rem;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }

    .card-label {
        font-size: 0.875rem;
        color: var(--color-text-muted);
        font-weight: 500;
        margin-bottom: 0.5rem;
    }

    .card-value {
        font-size: 2.25rem;
        font-weight: bold;
        color: var(--color-text-dark);
    }

    .card-secondary-value {
        font-size: 1.5rem;
        font-weight: bold;
        color: var(--color-text-dark);
    }

    .card-subtext {
        font-size: 0.875rem;
        color: var(--color-text-lighter);
        margin-top: 0.25rem;
    }

    .status-badge {
        color: white;
        padding: 0.25rem 0.5rem;
        border-radius: 0.25rem;
        font-size: 0.875rem;
        font-weight: 600;
        min-width: 3rem;
        text-align: center;
    }

    .progress-bar-container {
        flex: 1;
        background: var(--color-bg-progress);
        border-radius: 0.25rem;
        height: 1.5rem;
        position: relative;
        overflow: hidden;
    }

    .progress-bar-fill {
        height: 100%;
        transition: width 0.3s;
    }

    .status-count {
        font-weight: 600;
        color: var(--color-text-dark);
        min-width: 4rem;
        text-align: right;
    }

    table {
        width: 100%;
        border-collapse: collapse;
    }

    thead tr {
        background: var(--color-bg-header);
        border-bottom: 1px solid var(--color-border);
    }

    th {
        padding: 1rem;
        text-align: left;
        font-weight: 600;
        color: var(--color-text-muted);
        font-size: 0.875rem;
    }

    th:nth-child(2),
    th:nth-child(3) {
        text-align: center;
    }

    tbody tr {
        border-bottom: 1px solid var(--color-border);
    }

    tbody tr:hover {
        background: var(--color-bg-header);
    }

    td {
        padding: 1rem;
        color: var(--color-text-muted);
    }

    td:first-child {
        color: var(--color-text-dark);
        font-family: 'Courier New', monospace;
        font-size: 0.875rem;
    }

    td:nth-child(2),
    td:nth-child(3) {
        text-align: center;
    }

    .grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1.5rem;
    }

    .grid-section {
        margin-bottom: 2rem;
    }

    .flex-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .flex-center {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        flex: 1;
    }

    .card-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
    }

    .icon {
        font-size: 2rem;
    }

    .no-data {
        color: var(--color-text-muted);
    }

    .card-blue {
        border-left: 4px solid var(--color-blue);
    }

    .card-green {
        border-left: 4px solid var(--color-green);
    }

    .card-orange {
        border-left: 4px solid var(--color-orange);
    }

    .card-purple {
        border-left: 4px solid var(--color-purple);
    }
</style>

<div class="analytics-container">
    @php
        $todayStats = $this->getTodayStats();
        $allTimeStats = $this->getAllTimeStats();
        $endpointStats = $this->getRequestsByEndpoint();
    @endphp

    <!-- Today Stats -->
    <div class="analytics-section">
        <h2 class="analytics-title">Today</h2>

        <div class="grid" style="margin-bottom: 2rem;">
            <!-- Total Requests Card -->
            <div class="card card-blue">
                <div class="card-header">
                    <div>
                        <p class="card-label">Total Requests</p>
                        <p class="card-value">{{ $todayStats['total_requests'] }}</p>
                    </div>
                    <span class="icon">📊</span>
                </div>
            </div>

            <!-- Avg Duration Card -->
            <div class="card card-green">
                <div class="card-header">
                    <div>
                        <p class="card-label">Avg Duration</p>
                        <p class="card-value">{{ $todayStats['avg_duration'] }}ms</p>
                    </div>
                    <span class="icon">⚡</span>
                </div>
            </div>

            <!-- Top Client Card -->
            <div class="card card-orange">
                <div class="card-header">
                    <div>
                        <p class="card-label">Top Client</p>
                        <p class="card-secondary-value">{{ $todayStats['top_client']['name'] }}</p>
                        <p class="card-subtext">{{ $todayStats['top_client']['count'] }} requests</p>
                    </div>
                    <span class="icon">👤</span>
                </div>
            </div>
        </div>

        <!-- Status Codes Today -->
        <div class="card" style="margin-bottom: 2rem;">
            <h3 style="font-size: 1.125rem; font-weight: 600; margin-bottom: 1rem; color: var(--color-text-dark);">Status Codes Distribution</h3>

            <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                @php
                    $statusColors = [
                        '200' => '#10b981',
                        '201' => '#10b981',
                        '400' => '#f59e0b',
                        '401' => '#ef4444',
                        '404' => '#ef4444',
                        '429' => '#f59e0b',
                        '500' => '#ef4444',
                    ];
                @endphp

                @forelse($todayStats['by_status'] as $status => $count)
                    <div class="flex-row">
                        <div class="flex-center">
                            <span class="status-badge" style="background: {{ $statusColors[$status] ?? '#6b7280' }};">{{ $status }}</span>
                            <div class="progress-bar-container">
                                <div class="progress-bar-fill" style="background: {{ $statusColors[$status] ?? '#6b7280' }}; width: {{ ($count / $todayStats['total_requests']) * 100 }}%;"></div>
                            </div>
                        </div>
                        <span class="status-count">{{ $count }}</span>
                    </div>
                @empty
                    <p class="no-data">No requests today</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Endpoints Breakdown -->
    <div class="analytics-section">
        <h2 class="analytics-title">Top Endpoints</h2>

        <div class="card" style="overflow: hidden;">
            <table>
                <thead>
                    <tr>
                        <th>Endpoint</th>
                        <th>Requests</th>
                        <th>Avg Duration</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($endpointStats as $endpoint)
                        <tr>
                            <td>{{ $endpoint['path'] }}</td>
                            <td>{{ $endpoint['count'] }}</td>
                            <td>{{ $endpoint['avg_duration'] }}ms</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" style="padding: 2rem; text-align: center; color: var(--color-text-muted);">No data available</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- All Time Stats -->
    <div>
        <h2 class="analytics-title">All Time</h2>

        <div class="grid">
            <div class="card card-purple">
                <p class="card-label">Total All Time</p>
                <p class="card-value">{{ $allTimeStats['total_requests'] }}</p>
            </div>
        </div>
    </div>
</div>
