<?php

use App\Http\Controllers\DashboardController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Account routes
    Route::prefix('account')->name('account.')->group(function () {
        // Profile
        Route::get('/profile', [\App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
        
        // Custom profile update routes that redirect back to profile page
        Route::put('/profile/information', function (\Illuminate\Http\Request $request, \Laravel\Fortify\Contracts\UpdatesUserProfileInformation $updater) {
            $updater->update($request->user(), $request->all());
            
            $message = 'Profile updated successfully.';
            
            // Check if email was changed and needs verification
            $request->user()->refresh();
            if (!$request->user()->hasVerifiedEmail()) {
                $message .= ' Please verify your new email address.';
            }
            
            return redirect()->route('account.profile.edit')->with('status', $message);
        })->name('profile.information.update');
        
        Route::put('/profile/password', function (\Illuminate\Http\Request $request, \Laravel\Fortify\Contracts\UpdatesUserPasswords $updater) {
            $updater->update($request->user(), $request->all());
            
            return redirect()->route('account.profile.edit')->with('status', 'Password updated successfully.');
        })->name('profile.password.update');
        
        // Two Factor Authentication
        Route::post('/two-factor/enable', function (\Illuminate\Http\Request $request, \PragmaRX\Google2FA\Google2FA $google2fa) {
            $user = $request->user();
            
            if (!$user->two_factor_secret) {
                $user->forceFill([
                    'two_factor_secret' => encrypt($google2fa->generateSecretKey()),
                ])->save();
            }
            
            return redirect()->route('account.profile.edit')->with('status', 'Two factor authentication has been enabled. Please confirm it by entering the code from your authenticator app.');
        })->name('two-factor.enable');
        
        Route::post('/two-factor/confirm', function (\Illuminate\Http\Request $request, \PragmaRX\Google2FA\Google2FA $google2fa) {
            $request->validate([
                'code' => 'required|string',
            ]);
            
            $user = $request->user();
            
            if (!$google2fa->verifyKey(
                decrypt($user->two_factor_secret),
                $request->code
            )) {
                return back()->withErrors(['code' => 'The provided two factor authentication code was invalid.']);
            }
            
            $user->forceFill([
                'two_factor_confirmed_at' => now(),
                'two_factor_recovery_codes' => encrypt(json_encode(
                    \Illuminate\Support\Collection::times(8, function () {
                        return \Illuminate\Support\Str::random(10);
                    })->all()
                )),
            ])->save();
            
            return redirect()->route('account.profile.edit')->with([
                'status' => 'Two factor authentication has been confirmed and enabled.',
                'recovery_codes' => $user->two_factor_recovery_codes,
            ]);
        })->name('two-factor.confirm');
        
        Route::post('/two-factor/recovery-codes', function (\Illuminate\Http\Request $request) {
            $user = $request->user();
            
            $user->forceFill([
                'two_factor_recovery_codes' => encrypt(json_encode(
                    \Illuminate\Support\Collection::times(8, function () {
                        return \Illuminate\Support\Str::random(10);
                    })->all()
                )),
            ])->save();
            
            return redirect()->route('account.profile.edit')->with([
                'status' => 'Recovery codes have been regenerated.',
                'recovery_codes' => $user->two_factor_recovery_codes,
            ]);
        })->name('two-factor.recovery-codes');
        
        Route::delete('/two-factor/disable', function (\Illuminate\Http\Request $request) {
            $request->user()->forceFill([
                'two_factor_secret' => null,
                'two_factor_recovery_codes' => null,
                'two_factor_confirmed_at' => null,
            ])->save();
            
            return redirect()->route('account.profile.edit')->with('status', 'Two factor authentication has been disabled.');
        })->name('two-factor.disable');
        
        // Teams
        Route::resource('teams', \App\Http\Controllers\Account\TeamController::class);
        Route::post('teams/{team}/switch', [\App\Http\Controllers\Account\TeamController::class, 'switchTeam'])->name('teams.switch');
        
        // Team Invitations
        Route::post('teams/{team}/invitations', [\App\Http\Controllers\Account\TeamInvitationController::class, 'store'])->name('teams.invitations.store');
        Route::delete('teams/{team}/invitations/{invite}', [\App\Http\Controllers\Account\TeamInvitationController::class, 'destroy'])->name('teams.invitations.destroy');
        
        // Team Members
        Route::delete('teams/{team}/members/{user}', [\App\Http\Controllers\Account\TeamMemberController::class, 'destroy'])->name('teams.members.destroy');
    });
});

// Email Verification Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/email/verify', function () {
        return view('auth.verify-email');
    })->name('verification.notice');

    Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
        $request->fulfill();
        return redirect()->route('dashboard')->with('status', 'Your email has been verified!');
    })->middleware(['signed'])->name('verification.verify');

    Route::post('/email/verification-notification', function (Request $request) {
        $request->user()->sendEmailVerificationNotification();
        return back()->with('status', 'verification-link-sent');
    })->middleware(['throttle:6,1'])->name('verification.send');
});
