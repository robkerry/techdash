<?php

namespace App\Console\Commands;

use App\Helpers\TeamNameHelper;
use App\Models\Team;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class EnsureUsersHaveTeams extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:ensure-users-have-teams';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Ensure all users older than 5 minutes have at least one team assigned';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        // Use a lock to prevent overlapping executions across multiple servers
        $lock = Cache::lock('ensure-users-have-teams', 900); // 15 minutes lock duration

        if (! $lock->get()) {
            $this->info('Command is already running on another server. Skipping...');

            return Command::SUCCESS;
        }

        try {
            // Get users that are older than 5 minutes
            $fiveMinutesAgo = now()->subMinutes(5);

            // Get users without teams (using a subquery to check team membership)
            $usersWithoutTeams = User::where('created_at', '<=', $fiveMinutesAgo)
                ->whereDoesntHave('teams')
                ->get();

            if ($usersWithoutTeams->isEmpty()) {
                $this->info('All users have teams assigned.');

                return Command::SUCCESS;
            }

            $this->info("Found {$usersWithoutTeams->count()} user(s) without teams. Creating teams...");

            $created = 0;
            foreach ($usersWithoutTeams as $user) {
                try {
                    // Generate team name using TeamNameHelper
                    $teamName = TeamNameHelper::possessiveTeamName($user->name);

                    // Create the team
                    $team = Team::create([
                        'name' => $teamName,
                        'owner_id' => $user->id,
                    ]);

                    // Attach user to team
                    $user->attachTeam($team);

                    // Set as current team if user doesn't have one
                    if (! $user->current_team_id) {
                        $user->current_team_id = $team->id;
                        $user->save();
                    }

                    $created++;
                    $this->line("Created team '{$teamName}' for user {$user->email} (ID: {$user->id})");
                } catch (\Exception $e) {
                    $this->error("Failed to create team for user {$user->email} (ID: {$user->id}): {$e->getMessage()}");
                    Log::error("Failed to create team for user {$user->id}", [
                        'user_id' => $user->id,
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString(),
                    ]);
                }
            }

            $this->info("Successfully created {$created} team(s).");

            return Command::SUCCESS;
        } finally {
            $lock->release();
        }
    }
}
