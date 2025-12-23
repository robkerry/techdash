<?php

namespace App\Helpers;

use App\Models\PublicSuffix;
use Illuminate\Support\Facades\Cache;

class DomainHelper
{
    /**
     * Cache key for public suffixes.
     */
    private const CACHE_KEY = 'public_suffixes_set';

    /**
     * Cache duration in seconds (24 hours).
     */
    private const CACHE_DURATION = 86400;

    /**
     * Calculate the registerable domain from a hostname using the Public Suffix List.
     *
     * @param  string  $hostname  The hostname (e.g., "www.example.co.uk")
     * @return string|null The registerable domain (e.g., "example.co.uk") or null if not found
     */
    public static function calculateDomain(string $hostname): ?string
    {
        // Remove port if present
        $hostname = explode(':', $hostname)[0];

        // Normalize: remove leading/trailing dots and convert to lowercase
        $hostname = trim(strtolower($hostname), '.');

        if (empty($hostname)) {
            return null;
        }

        // Get cached suffix set for O(1) lookup
        $suffixSet = self::getSuffixSet();

        if (empty($suffixSet)) {
            // If no suffixes in database, fallback to simple logic
            return self::fallbackDomain($hostname);
        }

        // Work backwards from the hostname - try longest suffixes first
        // For "www.example.co.uk", try: "example.co.uk", "co.uk", "uk"
        // We want to match the LONGEST suffix first (e.g., "co.uk" not "uk")
        $parts = explode('.', $hostname);
        $partCount = count($parts);

        // We need at least 2 parts to have a domain
        if ($partCount < 2) {
            return $hostname;
        }

        // Try suffixes from longest to shortest (from right to left)
        // Start with the longest possible suffix (all parts except the first)
        // For "www.example.co.uk" (4 parts), try: i=3 ("example.co.uk"), i=2 ("co.uk"), i=1 ("uk")
        for ($i = $partCount - 1; $i >= 1; $i--) {
            // Build suffix from the rightmost $i parts
            $suffix = implode('.', array_slice($parts, -$i));

            // Check if this suffix exists in the PSL
            if (isset($suffixSet[$suffix])) {
                // Found a matching suffix! Extract the registerable domain
                // We need one more part before the suffix
                if ($partCount > $i) {
                    // Get the part before the suffix + the suffix
                    $domainParts = array_slice($parts, -(1 + $i));

                    return implode('.', $domainParts);
                }
            }
        }

        // If no match found, fallback to simple logic
        return self::fallbackDomain($hostname);
    }

    /**
     * Get the public suffix set as an associative array for O(1) lookup.
     * Uses caching to avoid database queries on every call.
     *
     * @return array<string, true> Associative array where keys are suffixes
     */
    private static function getSuffixSet(): array
    {
        return Cache::remember(self::CACHE_KEY, self::CACHE_DURATION, function () {
            $suffixes = PublicSuffix::getIcannSuffixes();

            // Convert to associative array for O(1) lookup
            $set = [];
            foreach ($suffixes as $suffix) {
                $normalized = strtolower(trim($suffix, '.'));
                if (! empty($normalized)) {
                    $set[$normalized] = true;
                }
            }

            return $set;
        });
    }

    /**
     * Clear the cached suffix set.
     * Call this after updating the Public Suffix List.
     */
    public static function clearCache(): void
    {
        Cache::forget(self::CACHE_KEY);
    }

    /**
     * Fallback domain calculation when PSL is not available.
     */
    private static function fallbackDomain(string $hostname): string
    {
        $parts = explode('.', $hostname);
        if (count($parts) >= 2) {
            return $parts[count($parts) - 2].'.'.$parts[count($parts) - 1];
        }

        return $hostname;
    }
}
