<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Mpociot\Teamwork\Facades\Teamwork;

class TeamInvitationController extends Controller
{

    /**
     * Store a newly created team invitation.
     */
    public function store(Request $request, Team $team)
    {
        // Only allow team owner to invite
        if (!$team->isOwnedBy(auth()->user())) {
            abort(403, 'You do not have permission to invite users to this team.');
        }

        $validated = $request->validate([
            'email' => ['required', 'email', 'max:255'],
        ]);

        try {
            // Check if user already has a pending invite
            if (Teamwork::hasPendingInvite($validated['email'], $team)) {
                return redirect()->route('account.teams.show', $team)
                    ->withErrors(['email' => 'An invitation has already been sent to this email address.']);
            }

            // Invite the user to the team (email first, then team)
            Teamwork::inviteToTeam($validated['email'], $team, function ($invite) {
                // Optional: Send notification email here
            });

            return redirect()->route('account.teams.show', $team)
                ->with('status', 'Invitation sent to ' . $validated['email'] . '.');
        } catch (\Exception $e) {
            return redirect()->route('account.teams.show', $team)
                ->withErrors(['email' => $e->getMessage()]);
        }
    }

    /**
     * Remove the specified team invitation.
     */
    public function destroy(Team $team, $inviteId)
    {
        // Only allow team owner to remove invitations
        if (!$team->isOwnedBy(auth()->user())) {
            abort(403, 'You do not have permission to remove invitations from this team.');
        }

        $invite = \Mpociot\Teamwork\TeamInvite::findOrFail($inviteId);

        if ($invite->team_id !== $team->id) {
            abort(403, 'This invitation does not belong to this team.');
        }

        $invite->delete();

        return redirect()->route('account.teams.show', $team)
            ->with('status', 'Invitation removed.');
    }
}

