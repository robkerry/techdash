<?php

namespace App\Providers;

use Illuminate\Mail\MailManager;
use Illuminate\Support\ServiceProvider;
use Symfony\Component\HttpClient\HttpClient;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->app->resolving(MailManager::class, function (MailManager $manager) {
            $manager->extend('emailit', function (array $config) {
                $apiKey = $config['api_key'] ?? env('EMAILIT_API_KEY');

                if (empty($apiKey)) {
                    throw new \InvalidArgumentException('Emailit API key is required. Set EMAILIT_API_KEY in your .env file.');
                }

                // Create HTTP client using Symfony's HttpClient (same as Laravel does internally)
                $httpClient = HttpClient::create();

                return new \App\Mail\Transport\EmailitTransport(
                    $httpClient,
                    $apiKey,
                );
            });
        });
    }
}
