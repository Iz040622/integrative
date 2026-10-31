<?php

namespace App\Http\Controllers;

use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use App\Models\Customer;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Http\Controllers\Controller;

class GoogleLoginController extends Controller
{
    // Step 1: Redirect user to Google
    public function redirectToGoogle()
    {
        /** @var \Laravel\Socialite\Two\AbstractProvider $provider */
        $provider = Socialite::driver('google');
        $provider = $provider->with(['prompt' => 'consent select_account']);
        return $provider->redirect();
    }

    // Step 2: Handle callback
    public function handleGoogleCallback()
    {
        try {
            /** @var \Laravel\Socialite\Two\AbstractProvider $provider */
            $provider = Socialite::driver('google');
            
            $googleUser = $provider->stateless()->user();

            $user = User::where('email', $googleUser->getEmail())->first();

            if (!$user) {
                // Create new user in `users` table
                $user = User::create([
                    'email'       => $googleUser->getEmail(),
                    'password'    => bcrypt(Str::random(16)),
                    'provider'    => 'google',
                    'provider_id' => $googleUser->getId(),
                ]);

                // Create related record in `customers` table
                Customer::create([
                    'first_name' => $googleUser->user['given_name'] ?? '',
                    'last_name'  => $googleUser->user['family_name'] ?? '',
                    'dob'        => null,
                    'user_id'    => $user->id,
                ]);
            }

            // Log the user in
            Auth::login($user);

            return redirect()->route('welcome')->with('success', 'Signed up successfully with Google!');
        } catch (\Throwable $e) {
            // Send user back to sign in with a helpful message
            return redirect()->route('login')
                ->withErrors(['google' => 'Google login failed: ' . $e->getMessage()]);
        }
    }
}
