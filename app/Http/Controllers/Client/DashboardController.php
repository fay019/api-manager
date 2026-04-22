<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $client = auth('client')->user()->load([
            'apiClients' => fn ($q) => $q
                ->with(['apiKeys' => fn ($q) => $q->latest('created_at')])
                ->withCount([
                    'apiKeys as active_keys_count' => fn ($q) => $q->where('is_active', true),
                    'requestLogs as total_requests' => fn ($q) => $q,
                    'requestLogs as success_requests' => fn ($q) => $q->whereBetween('status_code', [200, 299]),
                ]),
        ]);

        return view('client.dashboard', compact('client'));
    }
}
