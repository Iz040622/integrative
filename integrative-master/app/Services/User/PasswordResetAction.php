<?php

namespace App\Services\User;

use App\Models\User;
use Illuminate\Contracts\Container\Container;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class PasswordResetAction extends BaseAction
{
    public function execute(mixed ...$args): RedirectResponse
    {
        /** @var Request $request */
        $request = $args[0];
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'password' => 'required|min:6|confirmed',
        ]);

        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return back()->withErrors(['email' => 'User not found']);
        }

        $user->password = Hash::make($request->password);
        $user->reset_token = null;
        $user->reset_token_expires_at = null;
        $user->save();

        Session::forget('email');
        Session::forget('show_otp_modal');
        Session::forget('show_reset_modal');

        return redirect()->route('login')->with('success', 'Password reset successfully!');
    }
}
