<?php

namespace App\Helpers;

use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class TeamHelper
{
    /**
     * Get the current team for the authenticated user.
     */
    public static function currentTeam(): ?Team
    {
        $user = Auth::user();
        
        if (!$user) {
            return null;
        }

        return $user->currentTeam;
    }

    /**
     * Get the current team ID for the authenticated user.
     */
    public static function currentTeamId(): ?int
    {
        return static::currentTeam()?->id;
    }

    /**
     * Check if the authenticated user is the owner of the given team.
     */
    public static function isTeamOwner(?Team $team = null): bool
    {
        $user = Auth::user();
        $team = $team ?? static::currentTeam();

        if (!$user || !$team) {
            return false;
        }

        return $team->owner_id === $user->id;
    }

    /**
     * Check if the authenticated user is a member of the given team.
     */
    public static function isTeamMember(?Team $team = null): bool
    {
        $user = Auth::user();
        $team = $team ?? static::currentTeam();

        if (!$user || !$team) {
            return false;
        }

        return $user->teams->contains('id', $team->id);
    }

    /**
     * Check if the authenticated user can perform an action on the team.
     */
    public static function canManageTeam(?Team $team = null): bool
    {
        return static::isTeamOwner($team);
    }

    /**
     * Get all teams for the authenticated user.
     */
    public static function userTeams(): \Illuminate\Database\Eloquent\Collection
    {
        $user = Auth::user();

        if (!$user) {
            return collect();
        }

        return $user->teams;
    }

    /**
     * Switch the authenticated user's current team.
     */
    public static function switchTeam(Team $team): bool
    {
        $user = Auth::user();

        if (!$user || !static::isTeamMember($team)) {
            return false;
        }

        $user->current_team_id = $team->id;
        return $user->save();
    }
}

