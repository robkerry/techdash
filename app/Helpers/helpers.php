<?php

use App\Helpers\TeamHelper;
use App\Models\Team;
use App\Models\User;

if (!function_exists('current_team')) {
    /**
     * Get the current team for the authenticated user.
     */
    function current_team(): ?Team
    {
        return TeamHelper::currentTeam();
    }
}

if (!function_exists('current_team_id')) {
    /**
     * Get the current team ID for the authenticated user.
     */
    function current_team_id(): ?int
    {
        return TeamHelper::currentTeamId();
    }
}

if (!function_exists('is_team_owner')) {
    /**
     * Check if the authenticated user is the owner of the given team.
     */
    function is_team_owner(?Team $team = null): bool
    {
        return TeamHelper::isTeamOwner($team);
    }
}

if (!function_exists('is_team_member')) {
    /**
     * Check if the authenticated user is a member of the given team.
     */
    function is_team_member(?Team $team = null): bool
    {
        return TeamHelper::isTeamMember($team);
    }
}

if (!function_exists('can_manage_team')) {
    /**
     * Check if the authenticated user can manage the given team.
     */
    function can_manage_team(?Team $team = null): bool
    {
        return TeamHelper::canManageTeam($team);
    }
}

if (!function_exists('user_teams')) {
    /**
     * Get all teams for the authenticated user.
     */
    function user_teams()
    {
        return TeamHelper::userTeams();
    }
}

if (!function_exists('switch_team')) {
    /**
     * Switch the authenticated user's current team.
     */
    function switch_team(Team $team): bool
    {
        return TeamHelper::switchTeam($team);
    }
}

