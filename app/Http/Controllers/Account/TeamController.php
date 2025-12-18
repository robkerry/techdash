<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class TeamController extends Controller
{

    /**
     * Display a listing of the user's teams.
     */
    public function index()
    {
        $teams = auth()->user()->teams()->with('owner')->get();

        return view('account.teams.index', [
            'teams' => $teams,
            'currentTeam' => auth()->user()->currentTeam,
        ]);
    }

    /**
     * Show the form for creating a new team.
     */
    public function create()
    {
        return view('account.teams.create');
    }

    /**
     * Store a newly created team.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        $team = Team::create([
            'name' => $validated['name'],
            'owner_id' => auth()->id(),
        ]);

        // Add the user to the team
        auth()->user()->attachTeam($team);

        // Set as current team if user doesn't have one
        if (!auth()->user()->current_team_id) {
            auth()->user()->current_team_id = $team->id;
            auth()->user()->save();
        }

        return redirect()->route('account.teams.index')
            ->with('status', 'Team created successfully.');
    }

    /**
     * Show the form for editing the specified team.
     */
    public function edit(Team $team)
    {
        // Only allow team owner to edit
        if (!$team->isOwnedBy(auth()->user())) {
            abort(403, 'You do not have permission to edit this team.');
        }

        return view('account.teams.edit', [
            'team' => $team,
        ]);
    }

    /**
     * Update the specified team.
     */
    public function update(Request $request, Team $team)
    {
        // Only allow team owner to update
        if (!$team->isOwnedBy(auth()->user())) {
            abort(403, 'You do not have permission to update this team.');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        $team->update([
            'name' => $validated['name'],
        ]);

        return redirect()->route('account.teams.index')
            ->with('status', 'Team updated successfully.');
    }

    /**
     * Remove the specified team.
     */
    public function destroy(Team $team)
    {
        // Only allow team owner to delete
        if (!$team->isOwnedBy(auth()->user())) {
            abort(403, 'You do not have permission to delete this team.');
        }

        // Prevent deleting if it's the user's last team
        if (auth()->user()->teams()->count() <= 1) {
            throw ValidationException::withMessages([
                'team' => ['Cannot delete your last team. A user must always be a member of at least one team.'],
            ]);
        }

        // If this is the current team, switch to another team
        if (auth()->user()->current_team_id === $team->id) {
            $otherTeam = auth()->user()->teams()->where('id', '!=', $team->id)->first();
            if ($otherTeam) {
                auth()->user()->current_team_id = $otherTeam->id;
                auth()->user()->save();
            }
        }

        // Remove user from team before deleting
        auth()->user()->detachTeam($team);

        // Delete the team (this will cascade delete team_user relationships)
        $team->delete();

        return redirect()->route('account.teams.index')
            ->with('status', 'Team deleted successfully.');
    }

    /**
     * Display the specified team.
     */
    public function show(Team $team)
    {
        // Ensure user is a member of the team
        if (!auth()->user()->isTeamMember($team)) {
            abort(403, 'You are not a member of this team.');
        }

        $team->load(['users', 'owner']);
        $invites = \Mpociot\Teamwork\TeamInvite::where('team_id', $team->id)->get();

        return view('account.teams.show', [
            'team' => $team,
            'invites' => $invites,
            'isOwner' => $team->isOwnedBy(auth()->user()),
        ]);
    }

    /**
     * Switch the user's current team.
     */
    public function switchTeam(Request $request, Team $team)
    {
        // Ensure user is a member of the team
        if (!auth()->user()->isTeamMember($team)) {
            abort(403, 'You are not a member of this team.');
        }

        auth()->user()->current_team_id = $team->id;
        auth()->user()->save();

        return redirect()->route('account.teams.index')
            ->with('status', 'Switched to ' . $team->name . '.');
    }
}

