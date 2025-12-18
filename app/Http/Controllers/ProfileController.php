<?php

namespace App\Http\Controllers;

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
}

