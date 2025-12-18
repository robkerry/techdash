<?php

namespace App\Traits;

use App\Helpers\TeamHelper;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Trait for models that belong to a team.
 * 
 * Add this trait to any model that should be scoped to teams.
 * The model must have a `team_id` column.
 */
trait BelongsToTeam
{
    /**
     * Boot the trait.
     */
    public static function bootBelongsToTeam(): void
    {
        // Automatically scope queries to the current team
        static::addGlobalScope('team', function (Builder $builder) {
            $teamId = TeamHelper::currentTeamId();
            
            if ($teamId !== null) {
                $builder->where('team_id', $teamId);
            }
        });

        // Automatically set team_id when creating
        static::creating(function (Model $model) {
            if (empty($model->team_id)) {
                $model->team_id = TeamHelper::currentTeamId();
            }
        });
    }

    /**
     * Get the team that owns this model.
     */
    public function team(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Team::class);
    }

    /**
     * Scope a query to a specific team.
     */
    public function scopeForTeam(Builder $query, int $teamId): Builder
    {
        return $query->where('team_id', $teamId);
    }

    /**
     * Scope a query to the current team.
     */
    public function scopeForCurrentTeam(Builder $query): Builder
    {
        $teamId = TeamHelper::currentTeamId();
        
        if ($teamId === null) {
            return $query->whereRaw('1 = 0'); // Return no results if no team
        }

        return $query->where('team_id', $teamId);
    }

    /**
     * Check if this model belongs to the current team.
     */
    public function belongsToCurrentTeam(): bool
    {
        return $this->team_id === TeamHelper::currentTeamId();
    }

    /**
     * Check if this model belongs to a specific team.
     */
    public function belongsToTeam(int $teamId): bool
    {
        return $this->team_id === $teamId;
    }
}

