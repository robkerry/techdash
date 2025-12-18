<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Mpociot\Teamwork\Traits\UserHasTeams;
use Spatie\LaravelPasskeys\Models\Concerns\HasPasskeys;
use Spatie\LaravelPasskeys\Models\Concerns\InteractsWithPasskeys;

class User extends Authenticatable implements HasPasskeys, MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, InteractsWithPasskeys, Notifiable, TwoFactorAuthenticatable, UserHasTeams;

    /**
     * Boot the model.
     */
    protected static function boot(): void
    {
        parent::boot();

        // Custom ID generation: start at 100126697, increment by 7
        static::creating(function (User $user) {
            if (! $user->id) {
                $lastUser = static::orderBy('id', 'desc')->first();
                $nextId = $lastUser ? $lastUser->id + 7 : 100126697;
                $user->id = $nextId;
                
                // Update the AUTO_INCREMENT to the next expected value
                $tableName = Schema::getConnection()->getTablePrefix().'users';
                DB::statement("ALTER TABLE `{$tableName}` AUTO_INCREMENT = " . ($nextId + 7));
            }
        });

        // Ensure user always has at least one team (safety net only)
        // Note: Team creation is handled in CreateNewUser action during registration
        static::saved(function (User $user) {
            // Skip if user was just created (team creation handled in CreateNewUser)
            if ($user->wasRecentlyCreated) {
                return;
            }

            // Refresh to get latest team count
            $user->refresh();
            
            // Only create team if user truly has no teams (safety net for edge cases)
            if ($user->exists && $user->teams()->count() === 0) {
                // Create a default team if user has no teams (safety net)
                $team = Team::create([
                    'name' => \App\Helpers\TeamNameHelper::possessiveTeamName($user->name),
                    'owner_id' => $user->id,
                ]);

                $user->attachTeam($team);

                // Set as current team if user doesn't have one
                if (! $user->current_team_id) {
                    $user->current_team_id = $team->id;
                    $user->saveQuietly(); // Use saveQuietly to avoid recursion
                }
            }
        });
    }

    /**
     * Override detachTeam to prevent removing the last team.
     */
    public function detachTeam($team)
    {
        // Convert team to ID if needed
        $teamId = is_object($team) ? $team->id : $team;

        // Prevent removing the last team
        if ($this->teams()->count() <= 1) {
            throw new \Illuminate\Validation\ValidationException(
                validator([], []),
                ['team' => ['Cannot remove the last team. A user must always be a member of at least one team.']]
            );
        }

        // Call parent method
        return parent::detachTeam($team);
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'two_factor_confirmed_at' => 'datetime',
        ];
    }

    /**
     * Check if the user is the owner of a team.
     * Note: The UserHasTeams trait already provides currentTeam() method.
     */
    public function isTeamOwner(?\App\Models\Team $team = null): bool
    {
        $team = $team ?? $this->currentTeam;

        if (! $team) {
            return false;
        }

        return $team->owner_id === $this->id;
    }

    /**
     * Check if the user is a member of a team.
     */
    public function isTeamMember(?\App\Models\Team $team = null): bool
    {
        $team = $team ?? $this->currentTeam;

        if (! $team) {
            return false;
        }

        return $this->teams->contains('id', $team->id);
    }

    /**
     * Check if the user can manage a team.
     */
    public function canManageTeam(?\App\Models\Team $team = null): bool
    {
        return $this->isTeamOwner($team);
    }
}
