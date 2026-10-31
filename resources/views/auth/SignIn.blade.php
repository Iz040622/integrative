<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Sign In | Hotel Stripes Kuala Lumpur</title>
    <link rel="stylesheet" href="{{ asset('css/signUp.css') }}">
</head>

<body>
    @if ($errors->any())
        <div class="error-message">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <div class="container">
        <div class="card">
            <div class="left-panel">
                <div class="overlay"></div>
                <div class="branding">
                    <div class="logo">
                        <img src="{{ asset('storage/images/logo.png') }}" alt="Hotel Stripes Logo">
                    </div>
                </div>
            </div>

            <div class="right-panel">
                <div class="avatar">&#128274;</div>
                <h3>Login to your Account</h3>

                <form method="POST" class="signin-form" action="{{ route('signin.submit') }}">
                    @csrf

                    <input type="email" name="email" placeholder="Email" required>
                    @error('email')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                    <input type="password" name="password" placeholder="Password" id="password" required>
                    @error('password')
                        <div class="error-message">{{ $message }}</div>
                    @enderror

                    <div class="checkbox-group">
                        <input type="checkbox" name="remember">
                        <label>Remember me</label>
                    </div>

                    <button type="submit" class="submit-btn">LOGIN</button>

                    <p class="login-link">
                        Don’t have an account?
                        <a href="{{ route('signup.form') }}">Sign up</a>
                    </p>

                    <div class="divider1"><span>or</span></div>
                </form>

                <div class="social-buttons">
                    <a href="{{ route('google.login') }}" id="google-login">
                        <button>
                            <img src="{{ asset('storage/images/google_logo.png') }}" alt="Google Logo"
                                id="google_logo">Google
                        </button>
                    </a>
                    {{-- <a href="{{ route('facebook.redirect') }}" id="facebook-login">
                            <button>
                                <img src="{{ asset('storage/images/facebook_logo.png') }}" alt="Facebook Logo" id="facebook_logo">Facebook
                            </button>
                        </a> --}}
                </div>

                <div class="forgot-password">
                    <a href="javascript:void(0)" onclick="showForgotPasswordModalComponent()">Forgot Password?</a>
                </div>

                <div class="footer1">
                    <p>&copy; 2023 Hotel Stripes Kuala Lumpur. All rights reserved.</p>
                </div>
            </div>
        </div>
    </div>

    @include('components.forgot_password_modal')
    @if (session('show_otp_modal'))
        @include('components.verify_otp_modal')
    @endif

    @if (session('show_reset_modal'))
        @include('components.reset_password_modal')
    @endif

    <script>
        function showForgotPasswordModal() {
            document.getElementById('forgotPasswordModal').style.display = 'block';
        }

        function closeForgotPasswordModal() {
            document.getElementById('forgotPasswordModal').style.display = 'none';
        }

        function showVerifyOtpModal() {
            closeForgotPasswordModal();
            document.getElementById('verifyOtpModal').style.display = 'block';
        }

        function closeVerifyOtpModal() {
            document.getElementById('verifyOtpModal').style.display = 'none';
        }

        function showResetPasswordModal() {
            closeVerifyOtpModal();
            document.getElementById('resetPasswordModal').style.display = 'block';
        }

        document.addEventListener('DOMContentLoaded', function() {
            @if (session('show_otp_modal'))
                showVerifyOtpModal();
            @endif

            @if (session('show_reset_modal'))
                showResetPasswordModal();
            @endif
        });
    </script>


</body>

</html>
