<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use App\Models\Team;
use App\Notifications\TeamInvitationNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Mpociot\Teamwork\Facades\Teamwork;

class TeamInvitationController extends Controller
{
    /**
     * Store a newly created team invitation.
     */
    public function store(Request $request, Team $team)
    {
        // Only allow team owner to invite
        if (! $team->isOwnedBy(auth()->user())) {
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

            // Load the team owner relationship for the email
            $team->load('owner');

            // Invite the user to the team
            Teamwork::inviteToTeam($validated['email'], $team, function ($invite) use ($team, $validated) {
                // Send notification email
                Notification::route('mail', $validated['email'])
                    ->notify(new TeamInvitationNotification($team, $invite));
            });

            return redirect()->route('account.teams.show', $team)
                ->with('status', 'Invitation sent to '.$validated['email'].'.');
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
        if (! $team->isOwnedBy(auth()->user())) {
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

    /**
     * Accept a team invitation.
     */
    public function accept(Request $request, $token)
    {
        // Find the invitation by token
        $invite = \Mpociot\Teamwork\TeamInvite::where('accept_token', $token)->first();

        if (! $invite) {
            return redirect()->route('login')
                ->withErrors(['invitation' => 'Invalid or expired invitation token.']);
        }

        // Check if user exists with this email
        $user = \App\Models\User::where('email', $invite->email)->first();

        if (! $user) {
            // Create new user with blank name, random password, and verified email
            // Use forceFill to bypass mass assignment for email_verified_at
            $user = \App\Models\User::forceCreate([
                'email' => $invite->email,
                'name' => '',
                'password' => \Illuminate\Support\Facades\Hash::make(\Illuminate\Support\Str::random(32)),
                'email_verified_at' => now(),
            ]);
        } else {
            // User exists - mark email as verified since they clicked the invitation link
            if (! $user->hasVerifiedEmail()) {
                $user->forceFill([
                    'email_verified_at' => now(),
                ])->save();
            }
        }

        // Refresh to ensure all attributes are loaded
        $user->refresh();

        // Log the user in (after ensuring email is verified)
        \Illuminate\Support\Facades\Auth::login($user);

        // Accept the invitation (this adds user to team)
        // Pass the invite object, not the token
        if (Teamwork::acceptInvite($invite)) {
            // Refresh user to get updated team relationships
            $user->refresh();

            // If user has blank name, redirect to complete profile
            if (empty(trim($user->name))) {
                return redirect()->route('account.profile.complete')
                    ->with('status', 'You have successfully joined the team! Please complete your profile to continue.');
            }

            return redirect()->route('account.teams.index')
                ->with('status', 'You have successfully joined the team!');
        }

        return redirect()->route('login')
            ->withErrors(['invitation' => 'Failed to accept invitation.']);
    }

    /**
     * Deny a team invitation.
     */
    public function deny(Request $request, $token)
    {
        // Find the invitation by token
        $invite = \Mpociot\Teamwork\TeamInvite::where('deny_token', $token)->first();

        if (! $invite) {
            return redirect()->route('login')
                ->withErrors(['invitation' => 'Invalid or expired invitation token.']);
        }

        // Pass the invite object, not the token
        if (Teamwork::denyInvite($invite)) {
            return redirect()->route('login')
                ->with('status', 'You have declined the team invitation.');
        }

        return redirect()->route('login')
            ->withErrors(['invitation' => 'Failed to decline invitation.']);
    }
}
