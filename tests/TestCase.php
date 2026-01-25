<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    /**
     * Setup before ANY tests run.
     * Creates .env from .env.example BEFORE PHPUnit bootstraps the app.
     */
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        // Create .env from .env.example before app bootstrap
        $basePath = dirname(__DIR__);
        $envPath = $basePath.'/.env';
        $examplePath = $basePath.'/.env.example';

        // If .env doesn't exist, create it from .env.example
        if (! file_exists($envPath)) {
            if (file_exists($examplePath)) {
                copy($examplePath, $envPath);
            }
        } else {
            // .env exists - ensure it has a valid APP_KEY
            $content = file_get_contents($envPath);
            if (! preg_match('/^APP_KEY=base64:/', $content)) {
                // APP_KEY is missing or empty - add one from .env.example
                if (file_exists($examplePath)) {
                    $exampleContent = file_get_contents($examplePath);
                    if (preg_match('/^APP_KEY=base64:(.+?)$/m', $exampleContent, $matches)) {
                        $content = preg_replace('/^APP_KEY=.*$/m', 'APP_KEY='.$matches[0], $content);
                        if ($content === null) {
                            // No APP_KEY line found, add it
                            $content = 'APP_KEY='.$matches[0]."\n".$content;
                        }
                        file_put_contents($envPath, $content);
                    }
                }
            }
        }
    }
}
