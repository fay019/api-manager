<?php

namespace App\Http\Controllers;

use App\Services\AppSettingService;

class HomeController extends Controller
{
    public function __construct(private AppSettingService $settingService)
    {
    }

    public function index()
    {
        $showCredentials = $this->settingService->shouldShowCredentials();

        return view('home', compact('showCredentials'));
    }
}
