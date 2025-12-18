<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use App\Models\Team;
use App\Models\User;
use Illuminate\Http\Request;

class TeamMemberController extends Controller
{

    /**
     * Remove a member from the team.
     */
    public function destroy(Team $team, User $user)
    {
        // Only allow team owner to remove members
        if (!$team->isOwnedBy(auth()->user())) {
            abort(403, 'You do not have permission to remove members from this team.');
        }

        // Prevent removing the owner
        if ($team->owner_id === $user->id) {
            abort(403, 'Cannot remove the team owner.');
        }

        // Prevent removing if it's the user's last team
        if ($user->teams()->count() <= 1) {
            return redirect()->route('account.teams.show', $team)
                ->withErrors(['member' => 'Cannot remove user from their last team.']);
        }

        // If this is the user's current team, switch to another team
        if ($user->current_team_id === $team->id) {
            $otherTeam = $user->teams()->where('id', '!=', $team->id)->first();
            if ($otherTeam) {
                $user->current_team_id = $otherTeam->id;
                $user->save();
            }
        }

        $user->detachTeam($team);

        return redirect()->route('account.teams.show', $team)
            ->with('status', 'Member removed from team.');
    }
}

