<?php

namespace App\Services;

use App\Helpers\DomainHelper;
use Google\Client;
use Google\Service\SearchConsole;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GoogleSearchConsoleService
{
    /**
     * Get the OAuth2 client configured for Google Search Console.
     */
    public static function getClient(): Client
    {
        $client = new Client;
        $client->setClientId(config('services.google.client_id'));
        $client->setClientSecret(config('services.google.client_secret'));
        $client->setRedirectUri(route('websites.gsc.callback'));
        $client->setScopes([
            'https://www.googleapis.com/auth/webmasters.readonly',
        ]);
        $client->setAccessType('offline');
        $client->setPrompt(['consent', 'select_account']); // Force consent screen to get refresh token

        return $client;
    }

    /**
     * Get the authorization URL for OAuth2 flow.
     */
    public static function getAuthUrl(?string $state = null): string
    {
        $client = self::getClient();
        if ($state) {
            $client->setState($state);
        }

        return $client->createAuthUrl();
    }

    /**
     * Exchange authorization code for access token.
     */
    public static function exchangeCodeForToken(string $code): array
    {
        $client = self::getClient();
        $accessToken = $client->fetchAccessTokenWithAuthCode($code);

        if (isset($accessToken['error'])) {
            throw new \Exception('Error fetching access token: '.$accessToken['error']);
        }

        return $accessToken;
    }

    /**
     * Get list of verified Search Console sites.
     */
    public static function getSites(string $accessToken): array
    {
        $client = self::getClient();
        $client->setAccessToken($accessToken);

        $service = new SearchConsole($client);

        try {
            $sites = $service->sites->listSites();
            $verifiedSites = [];

            foreach ($sites->getSiteEntry() as $site) {
                // Only include verified sites
                if ($site->getPermissionLevel() !== 'siteUnverifiedUser') {
                    $verifiedSites[] = [
                        'siteUrl' => $site->getSiteUrl(),
                        'permissionLevel' => $site->getPermissionLevel(),
                    ];
                }
            }

            return $verifiedSites;
        } catch (\Exception $e) {
            Log::error('Error fetching GSC sites: '.$e->getMessage());

            throw $e;
        }
    }

    /**
     * Refresh an access token using a refresh token.
     */
    public static function refreshAccessToken(string $refreshToken): array
    {
        $client = self::getClient();
        $client->refreshToken($refreshToken);
        $token = $client->getAccessToken();

        if (isset($token['error'])) {
            throw new \Exception('Error refreshing access token: '.$token['error']);
        }

        return $token;
    }

    /**
     * Test redirect for a domain property to find the actual hostname.
     * For sc-domain: properties, we need to check if the root domain redirects.
     */
    public static function testDomainRedirect(string $domain): ?string
    {
        // Remove sc-domain: prefix if present
        $domain = str_replace('sc-domain:', '', $domain);

        // Ensure we have a protocol
        if (! str_starts_with($domain, 'http://') && ! str_starts_with($domain, 'https://')) {
            $domain = 'https://'.$domain;
        }

        try {
            // Follow redirects and get final URL
            /** @var \Illuminate\Http\Client\Response $response */
            $response = Http::timeout(10)
                ->withoutRedirecting()
                ->get($domain);

            $statusCode = method_exists($response, 'status') ? $response->status() : 0;

            // If we got a redirect, follow it only if same domain
            if ($statusCode >= 300 && $statusCode < 400) {
                $location = method_exists($response, 'header') ? $response->header('Location') : null;
                if ($location) {
                    // Make absolute URL if relative
                    if (! str_starts_with($location, 'http')) {
                        $parsed = parse_url($domain);
                        $location = ($parsed['scheme'] ?? 'https').'://'.($parsed['host'] ?? '').$location;
                    }

                    // Only follow redirect if destination has same registerable domain as source
                    $sourceHostname = parse_url($domain, PHP_URL_HOST);
                    $destinationHostname = parse_url($location, PHP_URL_HOST);

                    if ($sourceHostname && $destinationHostname) {
                        $sourceDomain = DomainHelper::calculateDomain($sourceHostname);
                        $destinationDomain = DomainHelper::calculateDomain($destinationHostname);

                        if ($sourceDomain && $destinationDomain && $sourceDomain === $destinationDomain) {
                            // Recursively follow redirects (with limit)
                            return self::followRedirects($location, 5);
                        }
                    }
                }
            }

            // If successful, return the hostname
            if ($statusCode >= 200 && $statusCode < 300) {
                $parsed = parse_url($domain);

                return $parsed['host'] ?? null;
            }
        } catch (\Exception $e) {
            Log::warning('Error testing domain redirect for '.$domain.': '.$e->getMessage());
        }

        // Fallback: return the domain hostname
        $parsed = parse_url($domain);

        return $parsed['host'] ?? null;
    }

    /**
     * Follow redirects recursively with a depth limit.
     */
    private static function followRedirects(string $url, int $maxDepth): ?string
    {
        if ($maxDepth <= 0) {
            return null;
        }

        try {
            /** @var \Illuminate\Http\Client\Response $response */
            $response = Http::timeout(10)
                ->withoutRedirecting()
                ->get($url);

            $statusCode = method_exists($response, 'status') ? $response->status() : 0;

            if ($statusCode >= 300 && $statusCode < 400) {
                $location = method_exists($response, 'header') ? $response->header('Location') : null;
                if ($location) {
                    // Make absolute URL if relative
                    if (! str_starts_with($location, 'http')) {
                        $parsed = parse_url($url);
                        $location = ($parsed['scheme'] ?? 'https').'://'.($parsed['host'] ?? '').$location;
                    }

                    // Only follow redirect if destination has same registerable domain as source
                    $sourceHostname = parse_url($url, PHP_URL_HOST);
                    $destinationHostname = parse_url($location, PHP_URL_HOST);

                    if ($sourceHostname && $destinationHostname) {
                        $sourceDomain = DomainHelper::calculateDomain($sourceHostname);
                        $destinationDomain = DomainHelper::calculateDomain($destinationHostname);

                        if ($sourceDomain && $destinationDomain && $sourceDomain === $destinationDomain) {
                            return self::followRedirects($location, $maxDepth - 1);
                        }
                    }
                }
            }

            if ($statusCode >= 200 && $statusCode < 300) {
                $parsed = parse_url($url);

                return $parsed['host'] ?? null;
            }
        } catch (\Exception $e) {
            Log::warning('Error following redirect for '.$url.': '.$e->getMessage());
        }

        $parsed = parse_url($url);

        return $parsed['host'] ?? null;
    }

    /**
     * Normalize GSC site URL to a standard format.
     * Handles both URL properties (https://example.com) and domain properties (sc-domain:example.com).
     */
    public static function normalizeSiteUrl(string $siteUrl): array
    {
        $isDomainProperty = str_starts_with($siteUrl, 'sc-domain:');

        if ($isDomainProperty) {
            $domain = str_replace('sc-domain:', '', $siteUrl);
            // Test redirect to find actual hostname
            $hostname = self::testDomainRedirect($domain);
            // Ensure URL has https:// prefix and / suffix
            $url = 'https://'.$hostname.'/';

            return [
                'url' => $url,
                'hostname' => $hostname,
                'domain' => $domain,
                'is_domain_property' => true,
                'original_site_url' => $siteUrl,
            ];
        }

        // URL property - normalize to ensure https:// prefix and / suffix
        $parsed = parse_url($siteUrl);
        $hostname = $parsed['host'] ?? null;

        // Reconstruct URL with https:// prefix and / suffix
        $scheme = $parsed['scheme'] ?? 'https';
        $normalizedUrl = $scheme.'://'.$hostname.'/';

        return [
            'url' => $normalizedUrl,
            'hostname' => $hostname,
            'domain' => $hostname,
            'is_domain_property' => false,
            'original_site_url' => $siteUrl,
        ];
    }
}
