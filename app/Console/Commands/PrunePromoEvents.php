<?php

namespace App\Console\Commands;

use App\Models\PromoEvent;
use Illuminate\Console\Command;

class PrunePromoEvents extends Command
{
    protected $signature = 'promo:prune-events {--days=180}';
    protected $description = 'Prune promo events older than N days';

    public function handle()
    {
        $days = $this->option('days');
        $cutoffDate = now()->subDays($days);

        $deletedCount = PromoEvent::where('created_at', '<', $cutoffDate)->delete();

        $this->info("Pruned {$deletedCount} promo events older than {$days} days.");

        return self::SUCCESS;
    }
}
