<?php

namespace App\Console\Commands;

use App\Models\TrackingLog;
use Illuminate\Console\Command;

class PruneTrackingLogs extends Command
{
    protected $signature = 'tracking:prune-logs {--days=90 : Delete tracking logs older than N days}';
    protected $description = 'Auto-prune tracking_logs older than N days (PII hygiene)';

    public function handle(): int
    {
        $days = max(1, (int) $this->option('days'));
        $deleted = TrackingLog::where('created_at', '<', now()->subDays($days))->delete();

        $this->info("Pruned {$deleted} tracking log(s) older than {$days} days.");

        return self::SUCCESS;
    }
}
