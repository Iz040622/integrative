<?php

namespace App\Services\User;

use App\Models\User;
use Illuminate\Contracts\Container\Container;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class SignUpCreateAction extends BaseAction
{
    public function execute(mixed ...$args): RedirectResponse
    {
        /** @var Request $request */
        $request = $args[0];
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name'  => 'required|string|max:255',
            'dob'        => 'required|date|before:' . now()->subYears(18)->toDateString(),
            'email'      => 'required|email|unique:users',
            'password'   => 'required|string|min:8',
        ], [
            'dob.before' => 'You must be 18 years or older to register.',
        ]);

        $user = User::create([
            'email'    => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $user->customer()->create([
            'first_name' => $request->first_name,
            'last_name'  => $request->last_name,
            'dob'        => $request->dob,
        ]);

        Auth::login($user);

        return redirect('/welcome')->with('success', 'Account Created Successfully!');
    }
}
