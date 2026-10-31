<?php

namespace App\Services\User;

use App\Mail\OtpMail;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Contracts\Container\Container;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;

class PasswordSendOtpAction extends BaseAction
{
    public function execute(mixed ...$args): RedirectResponse
    {
        /** @var Request $request */
        $request = $args[0];
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        $email = $request->email;
        $otp = rand(100000, 999999);

        $user = User::where('email', $email)->first();
        $user->reset_token = $otp;
        $user->reset_token_expires_at = Carbon::now()->addMinutes(10); // Token will expire in 10 min
        $user->save();

        Mail::to($email)->send(new OtpMail($otp));

        Session::put('email', $email);
        Session::flash('show_otp_modal', true);

        return redirect()->back()->with('success', 'OTP sent! Please check your email.');
    }
}
