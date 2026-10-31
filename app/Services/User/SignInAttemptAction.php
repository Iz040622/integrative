<?php

namespace App\Services\User;

use Illuminate\Contracts\Container\Container;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class SignInAttemptAction extends BaseAction
{
    public function execute(mixed ...$args): RedirectResponse
    {
        /** @var Request $request */
        $request = $args[0];

        $credentials = $request->validate([   //Validate the login credientials
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();

            $user = Auth::user();

            $userType = $this->getUserType($user);     //Detect the user type

            return match ($userType) {
                'admin'     => redirect()->route('admin.dashboard'),    // Middleware will check admin vs super admin
                'customer'  => redirect()->route('welcome'),
                'unknown'   => redirect('/'),
            };
        }

        return back()->withErrors([
            'email' => 'Invalid credentials provided. Please try again.'
        ])->onlyInput('email');
    }

    private function getUserType($user): string         //Detect user belong to which table
    {
        if ($user->admin) {
            return 'admin';
        }
        if ($user->customer) {
            return 'customer';
        }
        return 'unknown';
    }
}
