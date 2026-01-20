<?php

namespace App\Http\Controllers;

use App\Services\AppSettingService;
use Illuminate\Routing\Controller;

class DocsController extends Controller
{
    public function __construct(private AppSettingService $settingService)
    {
    }

    public function index()
    {
        if (!$this->settingService->isDocsIndexVisible()) {
            abort(404);
        }

        $visibleDocs = $this->settingService->getVisibleDocs();
        $allDocs = $this->settingService->getAllDocs();
        return view('docs.index', compact('visibleDocs', 'allDocs'));
    }

    /**
     * Show a specific documentation by name
     * Uses dynamic path from database
     */
    public function show(string $docName)
    {
        // Check visibility
        if (!$this->settingService->isDocumentationVisible($docName)) {
            abort(404);
        }

        // Get path from database
        $path = $this->settingService->getDocPath($docName);
        if (!$path) {
            abort(404);
        }

        // Read file content
        $filePath = base_path($path);
        if (!file_exists($filePath)) {
            abort(404);
        }

        $content = file_get_contents($filePath);
        $title = ucfirst($docName) . ' Documentation';

        return view('docs.show', compact('title', 'content'));
    }

    // Keep old routes for backwards compatibility
    public function api()
    {
        return $this->show('api');
    }

    public function database()
    {
        return $this->show('database');
    }

    public function deployment()
    {
        return $this->show('deployment');
    }

    public function readme()
    {
        return $this->show('readme');
    }
}
