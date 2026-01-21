<div style="padding: 2rem;">
    @php
        $todayStats = $this->getTodayStats();
        $allTimeStats = $this->getAllTimeStats();
        $endpointStats = $this->getRequestsByEndpoint();
    @endphp

    <!-- Today Stats -->
    <div style="margin-bottom: 2rem;">
        <h2 style="font-size: 1.875rem; font-weight: bold; margin-bottom: 1rem; color: #1f2937;">Today</h2>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
            <!-- Total Requests Card -->
            <div style="background: white; border-radius: 0.5rem; padding: 1.5rem; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1); border-left: 4px solid #3b82f6;">
                <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                    <div>
                        <p style="font-size: 0.875rem; color: #6b7280; font-weight: 500; margin-bottom: 0.5rem;">Total Requests</p>
                        <p style="font-size: 2.25rem; font-weight: bold; color: #1f2937;">{{ $todayStats['total_requests'] }}</p>
                    </div>
                    <span style="font-size: 2rem;">📊</span>
                </div>
            </div>

            <!-- Avg Duration Card -->
            <div style="background: white; border-radius: 0.5rem; padding: 1.5rem; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1); border-left: 4px solid #10b981;">
                <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                    <div>
                        <p style="font-size: 0.875rem; color: #6b7280; font-weight: 500; margin-bottom: 0.5rem;">Avg Duration</p>
                        <p style="font-size: 2.25rem; font-weight: bold; color: #1f2937;">{{ $todayStats['avg_duration'] }}ms</p>
                    </div>
                    <span style="font-size: 2rem;">⚡</span>
                </div>
            </div>

            <!-- Top Client Card -->
            <div style="background: white; border-radius: 0.5rem; padding: 1.5rem; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1); border-left: 4px solid #f59e0b;">
                <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                    <div>
                        <p style="font-size: 0.875rem; color: #6b7280; font-weight: 500; margin-bottom: 0.5rem;">Top Client</p>
                        <p style="font-size: 1.5rem; font-weight: bold; color: #1f2937;">{{ $todayStats['top_client']['name'] }}</p>
                        <p style="font-size: 0.875rem; color: #9ca3af; margin-top: 0.25rem;">{{ $todayStats['top_client']['count'] }} requests</p>
                    </div>
                    <span style="font-size: 2rem;">👤</span>
                </div>
            </div>
        </div>

        <!-- Status Codes Today -->
        <div style="background: white; border-radius: 0.5rem; padding: 1.5rem; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1); margin-bottom: 2rem;">
            <h3 style="font-size: 1.125rem; font-weight: 600; margin-bottom: 1rem; color: #1f2937;">Status Codes Distribution</h3>

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
                    <div style="display: flex; align-items: center; justify-content: space-between;">
                        <div style="display: flex; align-items: center; gap: 0.75rem; flex: 1;">
                            <span style="background: {{ $statusColors[$status] ?? '#6b7280' }}; color: white; padding: 0.25rem 0.5rem; border-radius: 0.25rem; font-size: 0.875rem; font-weight: 600; min-width: 3rem; text-align: center;">{{ $status }}</span>
                            <div style="flex: 1; background: #e5e7eb; border-radius: 0.25rem; height: 1.5rem; position: relative; overflow: hidden;">
                                <div style="background: {{ $statusColors[$status] ?? '#6b7280' }}; height: 100%; width: {{ ($count / $todayStats['total_requests']) * 100 }}%; transition: width 0.3s;"></div>
                            </div>
                        </div>
                        <span style="font-weight: 600; color: #1f2937; min-width: 4rem; text-align: right;">{{ $count }}</span>
                    </div>
                @empty
                    <p style="color: #6b7280;">No requests today</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Endpoints Breakdown -->
    <div style="margin-bottom: 2rem;">
        <h2 style="font-size: 1.875rem; font-weight: bold; margin-bottom: 1rem; color: #1f2937;">Top Endpoints</h2>

        <div style="background: white; border-radius: 0.5rem; overflow: hidden; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);">
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="background: #f9fafb; border-bottom: 1px solid #e5e7eb;">
                        <th style="padding: 1rem; text-align: left; font-weight: 600; color: #6b7280; font-size: 0.875rem;">Endpoint</th>
                        <th style="padding: 1rem; text-align: center; font-weight: 600; color: #6b7280; font-size: 0.875rem;">Requests</th>
                        <th style="padding: 1rem; text-align: center; font-weight: 600; color: #6b7280; font-size: 0.875rem;">Avg Duration</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($endpointStats as $endpoint)
                        <tr style="border-bottom: 1px solid #e5e7eb; hover:background: #f9fafb;">
                            <td style="padding: 1rem; color: #1f2937; font-family: 'Courier New', monospace; font-size: 0.875rem;">{{ $endpoint['path'] }}</td>
                            <td style="padding: 1rem; text-align: center; color: #6b7280;">{{ $endpoint['count'] }}</td>
                            <td style="padding: 1rem; text-align: center; color: #6b7280;">{{ $endpoint['avg_duration'] }}ms</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" style="padding: 2rem; text-align: center; color: #6b7280;">No data available</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- All Time Stats -->
    <div>
        <h2 style="font-size: 1.875rem; font-weight: bold; margin-bottom: 1rem; color: #1f2937;">All Time</h2>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem;">
            <div style="background: white; border-radius: 0.5rem; padding: 1.5rem; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1); border-left: 4px solid #8b5cf6;">
                <p style="font-size: 0.875rem; color: #6b7280; font-weight: 500; margin-bottom: 0.5rem;">Total All Time</p>
                <p style="font-size: 2.25rem; font-weight: bold; color: #1f2937;">{{ $allTimeStats['total_requests'] }}</p>
            </div>
        </div>
    </div>
</div>
