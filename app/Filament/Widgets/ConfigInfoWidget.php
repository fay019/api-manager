<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;

class ConfigInfoWidget extends Widget
{
    protected static ?int $sort = 2;

    protected string $view = 'filament.widgets.config-info';

    public function getGitVersion(): string
    {
        try {
            exec('git --version', $output, $status);
            if ($status === 0 && ! empty($output)) {
                $parts = explode(' ', trim($output[0]));

                return $parts[2] ?? 'N/A';
            }
        } catch (\Exception $e) {
            // Silently fail
        }

        return 'N/A';
    }

    public function getGitBranch(): string
    {
        try {
            exec('git rev-parse --abbrev-ref HEAD', $output, $status);
            if ($status === 0 && ! empty($output)) {
                return trim($output[0]);
            }
        } catch (\Exception $e) {
            // Silently fail
        }

        return 'N/A';
    }

    public function getGitCommit(): string
    {
        try {
            exec('git rev-parse --short HEAD', $output, $status);
            if ($status === 0 && ! empty($output)) {
                return trim($output[0]);
            }
        } catch (\Exception $e) {
            // Silently fail
        }

        return 'N/A';
    }
}
