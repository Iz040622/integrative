<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class ResetPasswordController extends Controller
{
    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'password' => 'required|min:6|confirmed',
        ]);

        $user = User::where('email', $request->email)->first();
        $user->password = Hash::make($request->password);
        $user->reset_token = null;
        $user->reset_token_expires_at = null;
        $user->save();

        // Clear session
        Session::forget('email');
        Session::forget('otp');

        return redirect()->route('login')->with('success', 'Password reset successfully!');
    }
}
