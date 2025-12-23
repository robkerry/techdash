<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Mpociot\Teamwork\TeamworkTeam;

class Team extends TeamworkTeam
{
    /**
     * Boot the model.
     */
    protected static function boot(): void
    {
        parent::boot();

        // Custom ID generation: start at 30021993, increment by 3
        static::creating(function (Team $team) {
            if (! $team->id) {
                $lastTeam = static::orderBy('id', 'desc')->first();
                $nextId = $lastTeam ? $lastTeam->id + 3 : 30021993;
                $team->id = $nextId;
                
                // Update the AUTO_INCREMENT to the next expected value
                $tableName = Schema::getConnection()->getTablePrefix().'teams';
                DB::statement("ALTER TABLE `{$tableName}` AUTO_INCREMENT = " . ($nextId + 3));
            }
        });
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'owner_id',
    ];

    /**
     * Get the owner of the team.
     */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    /**
     * Get all members of the team.
     */
    public function members(): HasMany
    {
        return $this->hasMany(User::class, 'current_team_id');
    }

    /**
     * Check if a user is a member of this team.
     */
    public function hasMember(User $user): bool
    {
        return $this->users->contains('id', $user->id);
    }

    /**
     * Check if a user is the owner of this team.
     */
    public function isOwnedBy(User $user): bool
    {
        return $this->owner_id === $user->id;
    }

    /**
     * Get the number of members in this team.
     */
    public function memberCount(): int
    {
        return $this->users()->count();
    }

    /**
     * Get all websites owned by this team.
     */
    public function websites(): HasMany
    {
        return $this->hasMany(Website::class);
    }
}
