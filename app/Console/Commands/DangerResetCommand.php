<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Artisan;

class DangerResetCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:danger-reset {--force : Skip production check and confirmation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'RESETS the application to PRE-INSTALL state (DESTRUCTIVE)';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->warn('!!! DANGER ZONE !!!');
        $this->warn('This will DELETE your database, reset your .env and removal of installation locks.');

        if (app()->environment('production') && ! $this->option('force')) {
            $this->error('Reset is FORBIDDEN in production environment.');
            return 1;
        }

        if (! $this->option('force')) {
            if (! $this->confirm('Are you ABSOLUTELY sure you want to reset the entire application?', false)) {
                $this->info('Reset aborted.');
                return 0;
            }

            $confirmation = $this->ask('Type "CONFIRMER" to execute the reset');
            if ($confirmation !== 'CONFIRMER') {
                $this->error('Reset canceled (incorrect confirmation string).');
                return 0;
            }
        }

        $this->info('Starting reset sequence...');

        // 1. Remove lock
        if (File::exists(storage_path('app/installed.lock'))) {
            File::delete(storage_path('app/installed.lock'));
            $this->line('✓ removed installed.lock');
        }

        // 2. Clear setup progress
        $setupDir = storage_path('app/setup');
        if (File::isDirectory($setupDir)) {
            File::cleanDirectory($setupDir);
            $this->line('✓ cleaned storage/app/setup/');
        }

        // 3. Reset DB (SQLite)
        $dbPath = database_path('database.sqlite');
        if (File::exists($dbPath)) {
            File::delete($dbPath);
            File::delete($dbPath.'-wal');
            File::delete($dbPath.'-shm');
            $this->line('✓ deleted SQLite database');
        } else {
            $this->warn('Non-SQLite database detected. Please clear tables manually.');
        }

        // 4. Backup and reset .env
        $envPath = base_path('.env');
        if (File::exists($envPath)) {
            File::copy($envPath, $envPath.'.backup.'.now()->format('YmdHis'));

            // We don't delete .env to keep basic structure, but we could strip APP_KEY
            // For now, removing the lock is enough to trigger PRE-INSTALL mode.
            $this->line('✓ .env backed up');
        }

        // 5. Purge logs
        $logFile = storage_path('logs/laravel.log');
        if (File::exists($logFile)) {
            File::put($logFile, '');
            $this->line('✓ logs truncated');
        }

        // 6. Clear caches
        Artisan::call('optimize:clear');
        $this->line('✓ caches cleared');

        $this->success('Application successfully reset to PRE-INSTALL state.');
        $this->info('You can now visit /setup/welcome to start over.');

        return 0;
    }

    protected function success(string $message): void
    {
        $this->output->writeln("<info>SUCCESS:</info> {$message}");
    }
}
