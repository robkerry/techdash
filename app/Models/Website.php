<?php

namespace App\Models;

use App\Helpers\DomainHelper;
use App\Services\GoogleSearchConsoleService;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Log;

class Website extends Model
{
    use HasUlids;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'team_id',
        'name',
        'url',
        'description',
        'hostname',
        'domain',
        'gsc_property',
        'gsc_access_token',
        'gsc_refresh_token',
        'gsc_last_refreshed_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'gsc_last_refreshed_at' => 'datetime',
        ];
    }

    /**
     * Boot the model.
     */
    protected static function boot(): void
    {
        parent::boot();

        // Auto-fill hostname and calculate domain when creating or updating
        static::saving(function (Website $website) {
            if ($website->url) {
                // Extract hostname from URL
                $hostname = parse_url($website->url, PHP_URL_HOST);
                $website->hostname = $hostname;

                // Calculate domain from hostname
                if ($hostname) {
                    $website->domain = DomainHelper::calculateDomain($hostname);
                }
            }
        });
    }

    /**
     * Get the team that owns the website.
     */
    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    /**
     * Refresh the Google Search Console access token if needed.
     */
    public function refreshGscTokenIfNeeded(): bool
    {
        if (! $this->gsc_refresh_token) {
            return false;
        }

        // Refresh if token is expired or will expire in the next 10 minutes
        // Tokens typically expire after 1 hour, so we refresh when older than 50 minutes
        if ($this->gsc_last_refreshed_at) {
            $expiresAt = $this->gsc_last_refreshed_at->copy()->addHour()->subMinutes(10);
            if ($expiresAt->isFuture()) {
                return false; // Token is still valid (has more than 10 minutes left)
            }
        }

        try {
            $tokens = GoogleSearchConsoleService::refreshAccessToken($this->gsc_refresh_token);

            $this->update([
                'gsc_access_token' => $tokens['access_token'],
                'gsc_refresh_token' => $tokens['refresh_token'] ?? $this->gsc_refresh_token,
                'gsc_last_refreshed_at' => now(),
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error("Failed to refresh GSC token for website {$this->id}: ".$e->getMessage());

            return false;
        }
    }

    /**
     * Get a valid Google Search Console access token, refreshing if needed.
     */
    public function getValidGscAccessToken(): ?string
    {
        if (! $this->gsc_access_token) {
            return null;
        }

        $this->refreshGscTokenIfNeeded();

        return $this->fresh()->gsc_access_token;
    }
}
