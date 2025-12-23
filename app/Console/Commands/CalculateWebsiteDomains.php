<?php

namespace App\Console\Commands;

use App\Helpers\DomainHelper;
use App\Models\Website;
use Illuminate\Console\Command;

class CalculateWebsiteDomains extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'websites:calculate-domains {--all : Recalculate domains for all websites}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Calculate registerable domains for websites using the Public Suffix List';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $query = Website::query();

        if (!$this->option('all')) {
            // Only calculate for websites without a domain
            $query->whereNull('domain');
        }

        $websites = $query->get();
        $total = $websites->count();

        if ($total === 0) {
            $this->info('No websites to process.');
            return Command::SUCCESS;
        }

        $this->info("Processing {$total} website(s)...");

        $bar = $this->output->createProgressBar($total);
        $bar->start();

        $updated = 0;

        foreach ($websites as $website) {
            if ($website->hostname) {
                $domain = DomainHelper::calculateDomain($website->hostname);
                $website->domain = $domain;
                $website->saveQuietly(); // Use saveQuietly to avoid triggering model events
                $updated++;
            }
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info("Successfully calculated domains for {$updated} website(s).");

        return Command::SUCCESS;
    }
}
