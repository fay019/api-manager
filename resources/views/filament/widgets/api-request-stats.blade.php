<x-filament-widgets::widget>
    @php
        $stats = $this->getStats();
    @endphp

    <div style="padding: 1.5rem;">
        <h3 style="font-size: 1.125rem; font-weight: 600; margin-bottom: 1rem; color: #1f2937;">API Request Statistics</h3>

        <!-- Today Stats Grid -->
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 1rem; margin-bottom: 1.5rem;">
            <!-- Today Total -->
            <div style="background: linear-gradient(135deg, #3b82f6 0%, #1e40af 100%); border-radius: 0.5rem; padding: 1rem; color: white;">
                <p style="font-size: 0.875rem; opacity: 0.9; margin-bottom: 0.5rem;">Today Requests</p>
                <p style="font-size: 2rem; font-weight: bold;">{{ $stats['today_total'] }}</p>
            </div>

            <!-- Success Rate -->
            <div style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); border-radius: 0.5rem; padding: 1rem; color: white;">
                <p style="font-size: 0.875rem; opacity: 0.9; margin-bottom: 0.5rem;">Success Rate</p>
                <p style="font-size: 2rem; font-weight: bold;">{{ $stats['success_rate'] }}%</p>
            </div>

            <!-- Errors Today -->
            <div style="background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); border-radius: 0.5rem; padding: 1rem; color: white;">
                <p style="font-size: 0.875rem; opacity: 0.9; margin-bottom: 0.5rem;">Errors Today</p>
                <p style="font-size: 2rem; font-weight: bold;">{{ $stats['today_errors'] }}</p>
            </div>

            <!-- Avg Duration -->
            <div style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); border-radius: 0.5rem; padding: 1rem; color: white;">
                <p style="font-size: 0.875rem; opacity: 0.9; margin-bottom: 0.5rem;">Avg Duration</p>
                <p style="font-size: 2rem; font-weight: bold;">{{ $stats['avg_duration'] }}ms</p>
            </div>
        </div>

        <!-- All Time -->
        <div style="background: #f3f4f6; border-radius: 0.5rem; padding: 1rem; border-left: 4px solid #8b5cf6;">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <p style="font-size: 0.875rem; color: #6b7280; margin-bottom: 0.25rem;">All Time Requests</p>
                    <p style="font-size: 1.875rem; font-weight: bold; color: #1f2937;">{{ $stats['all_time_total'] }}</p>
                </div>
                <span style="font-size: 2.5rem;">📊</span>
            </div>
        </div>
    </div>
</x-filament-widgets::widget>
