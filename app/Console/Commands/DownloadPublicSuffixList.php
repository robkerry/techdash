<?php

namespace App\Console\Commands;

use App\Helpers\DomainHelper;
use App\Models\PublicSuffix;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class DownloadPublicSuffixList extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'psl:download';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Download and process the Mozilla Public Suffix List, extracting ICANN public suffixes';

    /**
     * The URL of the Mozilla Public Suffix List.
     */
    private const PSL_URL = 'https://publicsuffix.org/list/public_suffix_list.dat';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Downloading Public Suffix List...');

        try {
            /** @var \Illuminate\Http\Client\Response $response */
            $response = Http::timeout(30)->get(self::PSL_URL);

            if (! $response->ok()) {
                $statusCode = method_exists($response, 'status') ? $response->status() : 'unknown';
                $this->error('Failed to download Public Suffix List. HTTP Status: '.$statusCode);

                return Command::FAILURE;
            }

            $content = method_exists($response, 'body') ? $response->body() : '';
            $this->info('Processing Public Suffix List...');

            // Parse the PSL file
            $icannSuffixes = $this->parsePublicSuffixList($content);

            $this->info("Found {$icannSuffixes->count()} ICANN public suffixes");

            // Use transaction for atomic update
            DB::transaction(function () use ($icannSuffixes) {
                // Clear existing ICANN suffixes
                PublicSuffix::where('type', 'icann')->delete();

                // Insert new suffixes in batches using insertOrIgnore to handle any duplicates
                $icannSuffixes->chunk(1000)->each(function ($chunk) {
                    $data = $chunk->map(fn ($suffix) => [
                        'suffix' => $suffix,
                        'type' => 'icann',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ])->toArray();

                    // Use insertOrIgnore to skip duplicates gracefully
                    DB::table('public_suffixes')->insertOrIgnore($data);
                });
            });

            // Clear the cache so the new suffixes are used immediately
            DomainHelper::clearCache();

            $this->info('Successfully updated Public Suffix List!');

            return Command::SUCCESS;

        } catch (\Exception $e) {
            $this->error('Error downloading Public Suffix List: '.$e->getMessage());

            return Command::FAILURE;
        }
    }

    /**
     * Parse the Public Suffix List content and extract ICANN suffixes.
     *
     * @param  string  $content  The PSL file content
     */
    private function parsePublicSuffixList(string $content): \Illuminate\Support\Collection
    {
        $lines = explode("\n", $content);
        $suffixes = collect();
        $inIcannSection = false;

        foreach ($lines as $line) {
            $line = trim($line);

            // Skip empty lines and comments
            if (empty($line) || str_starts_with($line, '//')) {
                // Check if we're entering the ICANN section
                if (str_contains($line, '===BEGIN ICANN DOMAINS===')) {
                    $inIcannSection = true;
                }
                // Check if we're leaving the ICANN section
                if (str_contains($line, '===END ICANN DOMAINS===')) {
                    $inIcannSection = false;
                }

                continue;
            }

            // Only process lines in the ICANN section
            if (! $inIcannSection) {
                continue;
            }

            // Remove any whitespace and comments
            $suffix = explode(' ', $line)[0];
            $suffix = trim($suffix);

            if (! empty($suffix)) {
                $suffixes->push($suffix);
            }
        }

        // Remove duplicates (case-insensitive and normalized) and sort by length (longest first) for efficient matching
        return $suffixes
            ->map(fn ($suffix) => mb_strtolower(trim($suffix))) // Normalize to lowercase
            ->filter(fn ($suffix) => ! empty($suffix)) // Remove empty strings
            ->unique() // Remove duplicates
            ->sortByDesc(fn ($suffix) => strlen($suffix)) // Sort by length (longest first)
            ->values();
    }
}
