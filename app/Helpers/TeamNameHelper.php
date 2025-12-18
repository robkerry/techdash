<?php

namespace App\Helpers;

class TeamNameHelper
{
    /**
     * Generate a possessive team name for a user.
     * Uses "'s" for most names, but "'" for names ending in 's' (like James, Chris, etc.).
     * Note: This follows the rule that names ending in 's' can use either "'s" or "'",
     * but we use "'" for simplicity and to match the user's requirement.
     *
     * @param string $name The user's name
     * @return string The possessive team name (e.g., "John's Team" or "James' Team")
     */
    public static function possessiveTeamName(string $name): string
    {
        $name = trim($name);
        
        // Handle empty names
        if (empty($name)) {
            return "My Team";
        }

        // Get the last character (case-insensitive check)
        $lastChar = strtolower(substr($name, -1));

        // Names ending in 's' use just an apostrophe (e.g., "James' Team", "Chris' Team")
        if ($lastChar === 's') {
            return $name . "' Team";
        }

        // All other names use "'s" (e.g., "John's Team", "Mary's Team")
        return $name . "'s Team";
    }
}

