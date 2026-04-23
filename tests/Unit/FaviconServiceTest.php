<?php

namespace Tests\Unit;

use App\Services\FaviconService;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class FaviconServiceTest extends TestCase
{
    public function test_it_generates_favicons_from_source(): void
    {
        Storage::fake('public');
        $sourcePath = 'settings/test-favicon.png';

        // Create a fake transparent PNG
        $img = imagecreatetruecolor(100, 100);
        imagesavealpha($img, true);
        $transparent = imagecolorallocatealpha($img, 0, 0, 0, 127);
        imagefill($img, 0, 0, $transparent);

        ob_start();
        imagepng($img);
        $content = ob_get_clean();
        imagedestroy($img);

        Storage::disk('public')->put($sourcePath, $content);

        $service = new FaviconService;
        $service->generateFromSource($sourcePath);

        $this->assertFileExists(public_path('favicon/favicon-16x16.png'));
        $this->assertFileExists(public_path('favicon/favicon-32x32.png'));
        $this->assertFileExists(public_path('favicon/apple-touch-icon.png'));
        $this->assertFileExists(public_path('favicon/android-192x192.png'));
        $this->assertFileExists(public_path('favicon/android-512x512.png'));
        $this->assertFileExists(public_path('favicon/site.webmanifest'));

        // Cleanup
        File::deleteDirectory(public_path('favicon'));
    }
}
