<?php

namespace App\Console\Commands;

use App\Models\Website;
use Illuminate\Console\Command;

class RefreshGscTokens extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'gsc:tokens:refresh';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Refresh Google Search Console access tokens that are older than 50 minutes';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        // Find websites with GSC tokens that were last refreshed more than 50 minutes ago
        $websites = Website::whereNotNull('gsc_refresh_token')
            ->whereNotNull('gsc_last_refreshed_at')
            ->where('gsc_last_refreshed_at', '<', now()->subMinutes(50))
            ->get();

        if ($websites->isEmpty()) {
            $this->info('No GSC tokens need refreshing.');

            return Command::SUCCESS;
        }

        $this->info("Found {$websites->count()} website(s) with tokens older than 50 minutes.");

        $refreshed = 0;
        $failed = 0;

        foreach ($websites as $website) {
            $this->line("Refreshing token for: {$website->hostname}");

            if ($website->refreshGscTokenIfNeeded()) {
                $refreshed++;
                $this->info("  ✓ Token refreshed successfully");
            } else {
                $failed++;
                $this->error("  ✗ Failed to refresh token");
            }
        }

        $this->info("Completed: {$refreshed} refreshed, {$failed} failed.");

        return Command::SUCCESS;
    }
}
