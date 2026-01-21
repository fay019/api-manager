<?php

namespace App\Filament\Widgets;

use App\Models\ApiRequestLog;
use Filament\Widgets\Widget;

class ApiRequestStatsWidget extends Widget
{
    protected string $view = 'filament.widgets.api-request-stats';

    protected static ?int $sort = 1;

    public function getStats(): array
    {
        $today = now()->startOfDay();

        $todayTotal = ApiRequestLog::whereDate('created_at', $today)->count();
        $todaySuccess = ApiRequestLog::whereDate('created_at', $today)
            ->whereBetween('status_code', [200, 299])
            ->count();
        $todayErrors = ApiRequestLog::whereDate('created_at', $today)
            ->whereBetween('status_code', [400, 599])
            ->count();

        $allTimeTotal = ApiRequestLog::count();
        $avgDuration = ApiRequestLog::whereDate('created_at', $today)->avg('duration_ms') ?? 0;

        return [
            'today_total' => $todayTotal,
            'today_success' => $todaySuccess,
            'today_errors' => $todayErrors,
            'all_time_total' => $allTimeTotal,
            'avg_duration' => round($avgDuration, 2),
            'success_rate' => $todayTotal > 0 ? round(($todaySuccess / $todayTotal) * 100, 1) : 0,
        ];
    }
}
