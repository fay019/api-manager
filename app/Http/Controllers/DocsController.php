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
        // Show documentation page even if no docs are visible
        // Empty state will guide users
        $visibleDocs = $this->settingService->getVisibleDocs();
        $allDocs = $this->settingService->getAllDocs();
        return view('docs.index', compact('visibleDocs', 'allDocs'));
    }

    /**
     * Show a specific documentation by name
     * Uses dynamic path from database
     * Converts Markdown to HTML for display
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

        $markdownContent = file_get_contents($filePath);

        // Convert Markdown to HTML
        $converter = new \League\CommonMark\GithubFlavoredMarkdownConverter([
            'html_input' => 'strip',
            'allow_unsafe_links' => false,
            'max_nesting_level' => 100,
        ]);
        $content = $converter->convert($markdownContent)->getContent();

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
