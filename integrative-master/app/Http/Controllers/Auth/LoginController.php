<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\User\UserActionFactory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    public function __construct(private readonly UserActionFactory $userActionFactory) {}

    public function showLoginForm()
    {
        return $this->userActionFactory->make('signin.show')->execute();
    }

    public function login(Request $request)
    {
        return $this->userActionFactory->make('signin.attempt')->execute($request);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect('/signin')->with('success', 'You have been logged out successfully.');
    }
}
