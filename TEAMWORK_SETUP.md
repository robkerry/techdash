# Teamwork Package Setup

## Overview

The `mpociot/teamwork` package has been properly configured with helper functions and traits for common team relationship requirements.

## Configuration

### Fixed Issues

1. **Config File Updated**: The `config/teamwork.php` file now correctly references `App\Models\Team::class` instead of the base `TeamworkTeam::class`.

2. **Team Model Enhanced**: The `Team` model now includes helpful relationship methods:
   - `owner()` - Get the team owner
   - `members()` - Get all team members
   - `hasMember(User $user)` - Check if user is a member
   - `isOwnedBy(User $user)` - Check if user owns the team
   - `memberCount()` - Get the number of members

3. **User Model Enhanced**: Added helper methods:
   - `isTeamOwner(?Team $team = null)` - Check if user owns a team
   - `isTeamMember(?Team $team = null)` - Check if user is a member
   - `canManageTeam(?Team $team = null)` - Check if user can manage a team

## Helper Functions

### Global Helper Functions

All helper functions are available globally via `app/Helpers/helpers.php`:

```php
// Get current team
$team = current_team();
$teamId = current_team_id();

// Check team ownership/membership
if (is_team_owner($team)) {
    // User owns the team
}

if (is_team_member($team)) {
    // User is a member
}

if (can_manage_team($team)) {
    // User can manage the team
}

// Get all user teams
$teams = user_teams();

// Switch current team
switch_team($team);
```

### TeamHelper Class

The `TeamHelper` class provides static methods for team operations:

```php
use App\Helpers\TeamHelper;

$team = TeamHelper::currentTeam();
$teamId = TeamHelper::currentTeamId();
$isOwner = TeamHelper::isTeamOwner($team);
$isMember = TeamHelper::isTeamMember($team);
$canManage = TeamHelper::canManageTeam($team);
$teams = TeamHelper::userTeams();
TeamHelper::switchTeam($team);
```

## BelongsToTeam Trait

For models that belong to a team, use the `BelongsToTeam` trait:

```php
use App\Traits\BelongsToTeam;

class YourModel extends Model
{
    use BelongsToTeam;
    
    // Your model must have a 'team_id' column
}
```

### Features

- **Automatic Scoping**: All queries are automatically scoped to the current team
- **Auto Team Assignment**: When creating records, `team_id` is automatically set
- **Query Scopes**: 
  - `forTeam($teamId)` - Scope to specific team
  - `forCurrentTeam()` - Scope to current team
- **Helper Methods**:
  - `team()` - Relationship to Team model
  - `belongsToCurrentTeam()` - Check if belongs to current team
  - `belongsToTeam($teamId)` - Check if belongs to specific team

### Example Usage

```php
// Automatically scoped to current team
$items = YourModel::all(); // Only returns items for current team

// Create a new item (team_id automatically set)
$item = YourModel::create(['name' => 'Test']);

// Check team ownership
if ($item->belongsToCurrentTeam()) {
    // Item belongs to current team
}

// Query specific team
$items = YourModel::forTeam($teamId)->get();
```

## Usage Examples

### In Controllers

```php
use App\Helpers\TeamHelper;

class YourController extends Controller
{
    public function index()
    {
        $team = TeamHelper::currentTeam();
        
        if (!TeamHelper::isTeamMember($team)) {
            abort(403, 'Not a team member');
        }
        
        // Your logic here
    }
}
```

### In Blade Views

```blade
@if(is_team_owner())
    <p>You are the team owner</p>
@endif

@if(can_manage_team())
    <a href="{{ route('team.settings') }}">Manage Team</a>
@endif
```

### In Models

```php
class Project extends Model
{
    use BelongsToTeam;
    
    // Automatically scoped to current team
    // team_id automatically set on creation
}
```

## Migration Status

The teamwork migration (`2025_12_18_121513_teamwork_setup_tables.php`) creates:
- `current_team_id` column on `users` table
- `teams` table
- `team_user` pivot table
- `team_invites` table

Make sure to run migrations:
```bash
php artisan migrate
```

## Next Steps

1. Create team management controllers and views
2. Add team switching functionality to the UI
3. Implement team-based authorization policies
4. Add team invitations functionality

