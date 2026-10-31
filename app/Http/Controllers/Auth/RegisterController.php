<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class RegisterController extends Controller
{
    public function showForm()
    {
        return view('auth.SignUp');
    }

    public function register(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name'  => 'required|string|max:255',
            'dob'        => 'required|date|before:' . now()->subYears(18)->toDateString(),
            'email'      => 'required|email|unique:users',
            'password'   => 'required|string|min:8',
        ], [
            'dob.before' => 'You must be 18 years or older to register.',
        ]);

        // Create user
        $user = User::create([
            'email'    => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Create related customer record via relationship
        $user->customer()->create([
            'first_name' => $request->first_name,
            'last_name'  => $request->last_name,
            'dob'        => $request->dob,
        ]);

        // Log the user in
        Auth::login($user);

        return redirect('/welcome')->with('success', 'Account Created Successfully!');
    }
}

