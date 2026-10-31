<?php

namespace App\Services\User;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Contracts\Container\Container;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Session;

class PasswordVerifyOtpAction extends BaseAction
{
    public function execute(mixed ...$args): RedirectResponse
    {
        /** @var Request $request */
        $request = $args[0];
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'otp' => 'required|digits:6',
        ]);

        $user = User::where('email', $request->email)
            ->where('reset_token', $request->otp)
            ->first();

        if (!$user) {
            return redirect()->back()->withErrors(['otp' => 'Invalid OTP'])->withInput();
        }

        if (Carbon::now()->greaterThan($user->reset_token_expires_at)) {
            return redirect()->back()->withErrors(['otp' => 'OTP expired'])->withInput();
        }

        Session::put('email', $request->email);
        Session::flash('show_otp_modal', true);
        Session::flash('show_reset_modal', true);

        return redirect()->back()->with('success', 'OTP verified. Set your new password.');
    }
}
