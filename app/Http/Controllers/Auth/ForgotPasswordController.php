<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\User\UserActionFactory;
use Illuminate\Http\Request;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Mail;
use App\Mail\OtpMail;
use Illuminate\Support\Facades\Hash;

class ForgotPasswordController extends Controller
{
    public function __construct(private readonly UserActionFactory $userActionFactory) {}

    // Show signin page (kept as is due to UI expectations)
    public function showForgotPasswordForm()
    {
        return view('auth.signin');
    }

    // Send OTP to user's email
    public function sendOtp(Request $request)
    {
        return $this->userActionFactory->make('password.send_otp')->execute($request);
    }

    // Verify OTP
    public function verifyOtp(Request $request)
    {
        return $this->userActionFactory->make('password.verify_otp')->execute($request);
    }

    // Reset Password
    public function resetPassword(Request $request)
    {
        return $this->userActionFactory->make('password.reset')->execute($request);
    }
}