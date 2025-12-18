<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasTeam
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && $user->teams()->count() === 0) {
            // This should never happen due to model events, but enforce it anyway
            // Create a default team
            $team = \App\Models\Team::create([
                'name' => \App\Helpers\TeamNameHelper::possessiveTeamName($user->name),
                'owner_id' => $user->id,
            ]);

            $user->attachTeam($team);
            $user->current_team_id = $team->id;
            $user->save();
        }

        // Ensure user has a current team set
        if ($user && !$user->current_team_id && $user->teams()->count() > 0) {
            $user->current_team_id = $user->teams()->first()->id;
            $user->save();
        }

        return $next($request);
    }
}

