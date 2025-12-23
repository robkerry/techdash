<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class ProfileController extends Controller
{
    /**
     * Show the user's profile page.
     */
    public function edit()
    {
        return view('profile.edit', [
            'user' => auth()->user(),
        ]);
    }

    /**
     * Show the profile completion page.
     */
    public function complete()
    {
        $user = auth()->user();

        // If profile is already complete, redirect to dashboard
        if (!empty(trim($user->name))) {
            return redirect()->route('dashboard');
        }

        return view('profile.complete', [
            'user' => $user,
        ]);
    }

    /**
     * Store the completed profile information.
     */
    public function completeStore(Request $request)
    {
        $user = auth()->user();

        // If profile is already complete, redirect to dashboard
        if (!empty(trim($user->name))) {
            return redirect()->route('dashboard');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        // Update user
        $user->name = $validated['name'];
        $user->password = Hash::make($validated['password']);
        $user->save();

        return redirect()->route('dashboard')
            ->with('status', 'Profile completed successfully!');
    }
}

