<?php

namespace App\Http\Controllers;

use App\Models\Website;
use App\Services\GoogleSearchConsoleService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class WebsiteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $team = auth()->user()->currentTeam;
        
        if (!$team) {
            abort(403, 'You must be a member of a team to view websites.');
        }

        $websites = $team->websites()->latest()->get();

        return view('websites.index', [
            'websites' => $websites,
            'team' => $team,
        ]);
    }

    /**
     * Redirect to GSC connection (websites can only be added via GSC).
     */
    public function create()
    {
        return redirect()->route('websites.gsc.connect');
    }

    /**
     * Initiate Google Search Console OAuth flow.
     */
    public function connectGsc()
    {
        $team = auth()->user()->currentTeam;
        
        if (!$team) {
            abort(403, 'You must be a member of a team to connect GSC.');
        }

        // Store team ID in session for callback
        Session::put('gsc_team_id', $team->id);

        $authUrl = GoogleSearchConsoleService::getAuthUrl();

        return redirect($authUrl);
    }

    /**
     * Handle Google Search Console OAuth callback.
     */
    public function gscCallback(Request $request)
    {
        $team = auth()->user()->currentTeam;
        $teamId = Session::get('gsc_team_id');

        if (!$team || !$teamId || $team->id != $teamId) {
            return redirect()->route('websites.create')
                ->withErrors(['gsc' => 'Invalid session. Please try again.']);
        }

        if ($request->has('error')) {
            return redirect()->route('websites.create')
                ->withErrors(['gsc' => 'Google authentication was cancelled or failed.']);
        }

        if (!$request->has('code')) {
            return redirect()->route('websites.create')
                ->withErrors(['gsc' => 'No authorization code received.']);
        }

        try {
            // Exchange code for tokens
            $tokens = GoogleSearchConsoleService::exchangeCodeForToken($request->code);

            // Get list of verified sites
            $sites = GoogleSearchConsoleService::getSites($tokens['access_token']);

            // Group by hostname to avoid duplicates
            $groupedSites = [];
            foreach ($sites as $site) {
                $normalized = GoogleSearchConsoleService::normalizeSiteUrl($site['siteUrl']);
                $hostname = $normalized['hostname'];

                // Group by hostname, keeping the first occurrence
                if (!isset($groupedSites[$hostname])) {
                    $groupedSites[$hostname] = [
                        'siteUrl' => $site['siteUrl'],
                        'normalized' => $normalized,
                        'permissionLevel' => $site['permissionLevel'],
                    ];
                }
            }

            // Store tokens and sites in session for the selection page
            Session::put('gsc_tokens', $tokens);
            Session::put('gsc_sites', array_values($groupedSites));

            return redirect()->route('websites.gsc.select');
        } catch (\Exception $e) {
            return redirect()->route('websites.create')
                ->withErrors(['gsc' => 'Error connecting to Google Search Console: '.$e->getMessage()]);
        }
    }

    /**
     * Show page to select GSC properties to add.
     */
    public function selectGscProperties()
    {
        $team = auth()->user()->currentTeam;
        $tokens = Session::get('gsc_tokens');
        $sites = Session::get('gsc_sites');

        if (!$team || !$tokens || !$sites) {
            return redirect()->route('websites.create')
                ->withErrors(['gsc' => 'Session expired. Please try connecting again.']);
        }

        return view('websites.gsc-select', [
            'team' => $team,
            'sites' => $sites,
        ]);
    }

    /**
     * Store selected GSC properties as websites.
     */
    public function storeGscProperties(Request $request)
    {
        $team = auth()->user()->currentTeam;
        $tokens = Session::get('gsc_tokens');
        $sites = Session::get('gsc_sites');

        if (!$team || !$tokens || !$sites) {
            return redirect()->route('websites.create')
                ->withErrors(['gsc' => 'Session expired. Please try connecting again.']);
        }

        $validated = $request->validate([
            'properties' => ['required', 'array', 'min:1'],
            'properties.*' => ['required', 'string'],
        ]);

        $created = 0;
        $errors = [];

        foreach ($validated['properties'] as $siteUrl) {
            // Find the site in our list
            $site = collect($sites)->firstWhere('siteUrl', $siteUrl);

            if (!$site) {
                $errors[] = "Site {$siteUrl} not found in available sites.";
                continue;
            }

            $normalized = $site['normalized'];

            // Check if website with this hostname already exists for this team
            $existing = $team->websites()
                ->where('hostname', $normalized['hostname'])
                ->first();

            if ($existing) {
                $errors[] = "Website with hostname {$normalized['hostname']} already exists.";
                continue;
            }

            try {
                // Create website
                $website = $team->websites()->create([
                    'name' => $normalized['hostname'],
                    'url' => $normalized['url'],
                    'hostname' => $normalized['hostname'],
                    'domain' => $normalized['domain'],
                    'gsc_property' => $siteUrl,
                    'gsc_access_token' => $tokens['access_token'],
                    'gsc_refresh_token' => $tokens['refresh_token'] ?? null,
                    'gsc_last_refreshed_at' => now(),
                ]);

                $created++;
            } catch (\Exception $e) {
                $errors[] = "Error creating website for {$normalized['hostname']}: ".$e->getMessage();
            }
        }

        // Clear session
        Session::forget(['gsc_tokens', 'gsc_sites', 'gsc_team_id']);

        $message = "Successfully created {$created} website(s).";
        if (!empty($errors)) {
            $message .= ' Some errors occurred: '.implode(' ', $errors);
        }

        return redirect()->route('websites.index')
            ->with('status', $message)
            ->with('errors', $errors);
    }

    /**
     * Store a newly created resource in storage.
     * Note: Manual website creation is disabled - websites must be added via GSC.
     */
    public function store(Request $request)
    {
        return redirect()->route('websites.gsc.connect')
            ->withErrors(['gsc' => 'Websites can only be added through Google Search Console.']);
    }

    /**
     * Display the specified resource.
     */
    public function show(Website $website)
    {
        $team = auth()->user()->currentTeam;
        
        if (!$team || $website->team_id !== $team->id) {
            abort(403, 'You do not have permission to view this website.');
        }

        return view('websites.show', [
            'website' => $website,
            'team' => $team,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Website $website)
    {
        $team = auth()->user()->currentTeam;
        
        if (!$team || $website->team_id !== $team->id) {
            abort(403, 'You do not have permission to edit this website.');
        }

        return view('websites.edit', [
            'website' => $website,
            'team' => $team,
        ]);
    }

    /**
     * Update the specified resource in storage.
     * Only the name field can be updated - other fields are managed by GSC.
     */
    public function update(Request $request, Website $website)
    {
        $team = auth()->user()->currentTeam;
        
        if (!$team || $website->team_id !== $team->id) {
            abort(403, 'You do not have permission to update this website.');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        $website->update([
            'name' => $validated['name'],
        ]);

        return redirect()->route('websites.index')
            ->with('status', 'Website updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Website $website)
    {
        $team = auth()->user()->currentTeam;
        
        if (!$team || $website->team_id !== $team->id) {
            abort(403, 'You do not have permission to delete this website.');
        }

        $website->delete();

        return redirect()->route('websites.index')
            ->with('status', 'Website deleted successfully.');
    }
}
