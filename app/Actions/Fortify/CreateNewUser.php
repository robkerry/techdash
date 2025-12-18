<?php

namespace App\Actions\Fortify;

use App\Helpers\TeamNameHelper;
use App\Models\Team;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array<string, string>  $input
     */
    public function create(array $input): User
    {
        Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique(User::class),
            ],
            'password' => $this->passwordRules(),
        ])->validate();

        $user = User::create([
            'name' => $input['name'],
            'email' => $input['email'],
            'password' => Hash::make($input['password']),
        ]);

        // Create a default team for the user
        $team = Team::create([
            'name' => TeamNameHelper::possessiveTeamName($user->name),
            'owner_id' => $user->id,
        ]);

        // Add user to the team and set as current team using the trait's method
        $user->attachTeam($team);
        $user->current_team_id = $team->id;
        $user->saveQuietly(); // Use saveQuietly to avoid triggering the saved event that checks for teams

        // Send email verification notification
        $user->sendEmailVerificationNotification();

        return $user;
    }
}
