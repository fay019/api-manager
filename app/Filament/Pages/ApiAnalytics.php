<?php

namespace App\Filament\Pages;

use App\Models\ApiRequestLog;
use BackedEnum;
use Filament\Pages\Page;
use UnitEnum;

class ApiAnalytics extends Page
{
    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-chart-bar';

    protected string $view = 'filament.pages.api-analytics';

    protected static string|UnitEnum|null $navigationGroup = 'Analytics';

    protected static ?int $navigationSort = 1;

    public function getTitle(): string
    {
        return __('filament.nav.analytics');
    }

    public function getTodayStats(): array
    {
        $today = now()->startOfDay();

        $totalRequests = ApiRequestLog::whereDate('created_at', $today)->count();

        $byStatus = ApiRequestLog::whereDate('created_at', $today)
            ->groupBy('status_code')
            ->selectRaw('status_code, count(*) as count')
            ->pluck('count', 'status_code')
            ->toArray();

        $avgDuration = ApiRequestLog::whereDate('created_at', $today)
            ->avg('duration_ms') ?? 0;

        $topClient = ApiRequestLog::whereDate('created_at', $today)
            ->groupBy('api_client_id')
            ->selectRaw('api_client_id, count(*) as count')
            ->orderByDesc('count')
            ->first();

        $topClientName = 'N/A';
        if ($topClient && $topClient->api_client_id) {
            $topClientName = $topClient->apiClient?->name ?? "Client #{$topClient->api_client_id}";
        }

        return [
            'total_requests' => $totalRequests,
            'by_status' => $byStatus,
            'avg_duration' => round($avgDuration, 2),
            'top_client' => [
                'name' => $topClientName,
                'count' => $topClient?->count ?? 0,
            ],
        ];
    }

    public function getAllTimeStats(): array
    {
        $totalRequests = ApiRequestLog::count();

        $byStatus = ApiRequestLog::groupBy('status_code')
            ->selectRaw('status_code, count(*) as count')
            ->pluck('count', 'status_code')
            ->toArray();

        return [
            'total_requests' => $totalRequests,
            'by_status' => $byStatus,
        ];
    }

    public function getRequestsByEndpoint(): array
    {
        return ApiRequestLog::groupBy('path')
            ->selectRaw('path, count(*) as count, AVG(duration_ms) as avg_duration')
            ->orderByDesc('count')
            ->limit(10)
            ->get()
            ->map(fn ($item) => [
                'path' => $item->path,
                'count' => $item->count,
                'avg_duration' => round($item->avg_duration, 2),
            ])
            ->toArray();
    }
}
