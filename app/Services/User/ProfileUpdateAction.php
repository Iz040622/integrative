<?php

namespace App\Services\User;

use Illuminate\Contracts\Container\Container;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfileUpdateAction extends BaseAction
{
    public function execute(mixed ...$args): RedirectResponse
    {
        /** @var Request $request */
        $request = $args[0];
        $user = Auth::user()->load('customer');

        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name'  => 'required|string|max:255',
            'email'      => 'required|email|unique:users,email,' . $user->id,
            'dob'        => 'nullable|date',
            'password'   => 'nullable|min:8',
        ]);

        $user->update([
            'first_name' => $request->first_name,
            'last_name'  => $request->last_name,
            'email'      => $request->email,
            'password' => $request->filled('password') ? Hash::make($request->password) : $user->password,
        ]);

        if ($user->customer) {
            $user->customer->update([
                'dob' => $request->dob,
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
            ]);
        }

        return redirect()->route('profile.show')->with('success', 'Profile updated successfully!');
    }
}
