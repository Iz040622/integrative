<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Sign Up | Hotel Stripes Kuala Lumpur</title>
    <link rel="stylesheet" href="{{ asset('css/signUp.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>

<body>
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
                @if ($errors->any())
                    <div class="error-messages">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <div class="avatar">&#128100;</div>
                <h3>Sign Up</h3>

                <form method="POST" class="signUp-form" action="{{ route('signup.submit') }}">
                    @csrf

                    <div class="name-group">
                        <input type="text" name="first_name" placeholder="First Name" required>
                        @error('first_name')
                            <div class="error">{{ $message }}</div>
                        @enderror
                        <input type="text" name="last_name" placeholder="Last Name" required>
                        @error('last_name')
                            <div class="error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="dob-group">
                        <input type="date" id="dob" name="dob" placeholder="Date of Birth" required>
                    </div>

                    <input type="email" name="email" placeholder="Email Address" required>
                    @error('email')
                        <div class="error">{{ $message }}</div>
                    @enderror
                    <input type="password" name="password" placeholder="Password" required>
                    @error('password')
                        <div class="error">{{ $message }}</div>
                    @enderror

                    <div class="checkbox-group">
                        <input type="checkbox" name="terms" required>
                        <label>I agree to the <a href="#" class="terms-link">Term & Condition</a></label>
                    </div>

                    <button type="submit" class="btn">Sign Up</button>

                    <p class="login-link">Already have an account? <a href="{{ route('login') }}">Login</a></p>

                    <div class="divider">
                        <span>or</span>
                    </div>
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

                <div class="footer">
                    <p>&copy; 2023 Hotel Stripes Kuala Lumpur. All rights reserved.</p>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const today = new Date();
            const dobInput = document.getElementById("dob");
            const form = document.querySelector(".signUp-form");

            // Prevent under 18 on submit
            form.addEventListener("submit", function(e) {
                const dobValue = dobInput.value;
                if (!dobValue) return; // if no dob entered, required will handle it

                const dob = new Date(dobValue);
                let age = today.getFullYear() - dob.getFullYear();
                const monthDiff = today.getMonth() - dob.getMonth();
                const dayDiff = today.getDate() - dob.getDate();

                // Adjust if birthday hasn’t occurred yet this year
                if (monthDiff < 0 || (monthDiff === 0 && dayDiff < 0)) {
                    age--;
                }

                if (age < 18) {
                    e.preventDefault(); // stop form from submitting
                    alert("You must be at least 18 years old to register.");
                    dobInput.focus();
                }
            });

            // Optional: prevent selecting future date
            dobInput.max = today.toISOString().split("T")[0];
        });
    </script>


</body>

</html>
