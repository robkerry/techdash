<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureProfileComplete
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // Check if user needs to complete profile (blank name)
        if ($user && empty(trim($user->name))) {
            // Allow access to profile completion page and logout
            if (!$request->routeIs('account.profile.complete') && 
                !$request->routeIs('logout') &&
                !$request->routeIs('account.profile.complete.store')) {
                return redirect()->route('account.profile.complete')
                    ->with('warning', 'Please complete your profile to continue.');
            }
        }

        return $next($request);
    }
}
