<?php

namespace App\Services;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class FaviconService
{
    protected string $outputPath;

    public function __construct()
    {
        $this->outputPath = public_path('favicon');
    }

    public function generateFromSource(string $sourcePath, bool $rounded = false): void
    {
        if (! File::exists($this->outputPath)) {
            File::makeDirectory($this->outputPath, 0755, true);
        }

        $this->cleanOldFiles();

        $fullSourcePath = Storage::disk('public')->path($sourcePath);

        $sizes = [
            ['name' => 'favicon-16x16.png', 'width' => 16, 'height' => 16],
            ['name' => 'favicon-32x32.png', 'width' => 32, 'height' => 32],
            ['name' => 'apple-touch-icon.png', 'width' => 180, 'height' => 180],
            ['name' => 'android-192x192.png', 'width' => 192, 'height' => 192],
            ['name' => 'android-512x512.png', 'width' => 512, 'height' => 512],
        ];

        foreach ($sizes as $size) {
            $this->resizeAndSave($fullSourcePath, $this->outputPath.'/'.$size['name'], $size['width'], $size['height'], $rounded);
        }

        $this->generateWebManifest();
    }

    protected function resizeAndSave(string $source, string $destination, int $width, int $height, bool $rounded = false): void
    {
        $imageInfo = getimagesize($source);
        if (! $imageInfo) {
            return;
        }

        $mime = $imageInfo['mime'];
        $srcImg = match ($mime) {
            'image/jpeg' => imagecreatefromjpeg($source),
            'image/png' => imagecreatefrompng($source),
            'image/webp' => imagecreatefromwebp($source),
            default => null,
        };

        if (! $srcImg) {
            return;
        }

        $dstImg = imagecreatetruecolor($width, $height);

        // Preserve transparency
        imagealphablending($dstImg, false);
        imagesavealpha($dstImg, true);
        $transparent = imagecolorallocatealpha($dstImg, 255, 255, 255, 127);
        imagefilledrectangle($dstImg, 0, 0, $width, $height, $transparent);

        imagecopyresampled($dstImg, $srcImg, 0, 0, 0, 0, $width, $height, $imageInfo[0], $imageInfo[1]);

        if ($rounded) {
            $mask = imagecreatetruecolor($width, $height);
            $black = imagecolorallocate($mask, 0, 0, 0);
            $magenta = imagecolorallocate($mask, 255, 0, 255);
            imagefill($mask, 0, 0, $magenta);
            imagecolortransparent($mask, $magenta);
            imagefilledellipse($mask, $width / 2, $height / 2, $width, $height, $black);

            for ($x = 0; $x < $width; $x++) {
                for ($y = 0; $y < $height; $y++) {
                    $color = imagecolorat($mask, $x, $y);
                    if ($color === $magenta) {
                        imagesetpixel($dstImg, $x, $y, $transparent);
                    }
                }
            }
            imagedestroy($mask);
        }

        imagepng($dstImg, $destination);

        imagedestroy($srcImg);
        imagedestroy($dstImg);
    }

    protected function generateWebManifest(): void
    {
        $manifest = [
            'name' => config('app.name'),
            'short_name' => config('app.name'),
            'icons' => [
                [
                    'src' => '/favicon/android-192x192.png',
                    'sizes' => '192x192',
                    'type' => 'image/png',
                ],
                [
                    'src' => '/favicon/android-512x512.png',
                    'sizes' => '512x512',
                    'type' => 'image/png',
                ],
            ],
            'theme_color' => '#ffffff',
            'background_color' => '#ffffff',
            'display' => 'standalone',
        ];

        File::put($this->outputPath.'/site.webmanifest', json_encode($manifest, JSON_PRETTY_PRINT));
    }

    protected function cleanOldFiles(): void
    {
        if (File::exists($this->outputPath)) {
            File::cleanDirectory($this->outputPath);
        }
    }
}
