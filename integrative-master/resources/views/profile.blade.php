<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Profile | Hotel Stripes Kuala Lumpur</title>
    <link rel="stylesheet" href="{{ asset('css/profile.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
    <div class="container">
        <div class="sidebar">
            <div class="logo">
                <img src="{{ asset('storage/images/logo.png') }}" alt="Hotel Stripes Logo">
            </div>
            <nav>
                <ul>
                    <li><a href="welcome">Home</a></li>
                    <li><a href="#">About Us</a></li>
                    <li><a href="#">Support</a></li>
                    <li><a href="{{ url('booking/create') }}">Booking</a></li>
                    <li><a href="{{ url('/cart') }}">Cart</a></li>
                    <li><a href="{{ url('profile') }}" class="active">Profile</a></li>
                </ul>
            </nav>
        </div>
        <div class="main-content">
            <h2>My Profile</h2>

            {{-- Success message --}}
             @if(session('success'))
            <p style="color: green;">{{ session('success') }}</p>
            @endif

            <div class="profile-card">
                <div class="avatar">👤</div>
                <form action="{{ route('profile.update') }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="name-group">
                        <div class="input-wrapper">
                            <label for="first_name">First Name</label>
                            <input type="text" name="first_name" placeholder="First Name" value="{{ old('first_name', $user->customer->first_name) }}" required>
                        </div>
                        <div class="input-wrapper">
                            <label for="last_name">Last Name</label>
                            <input type="text" name="last_name" placeholder="Last Name" value="{{ old('last_name', $user->customer->last_name) }}" required>
                        </div>
                    </div>
                    <div class="dob-group">
                        <label for="dob">Date of Birth</label>
                        <input type="date" id="dob" name="dob" class="flatpickr-input" placeholder="Select Date" value="{{ old('dob', $user->customer->dob) }}" required>
                    </div>
                    <input type="email" name="email" placeholder="Email Address" value="{{ old('email', $user->email) }}" required>
                    <input type="password" name="password" placeholder="New Password" required>
                    <button type="submit" class="btn">Save Changes</button>
                </form>
                <p class="edit-note">Click "Save Changes" to update your profile.</p>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
</body>
</html>