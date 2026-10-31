<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Hotel Stripes Kuala Lumpur</title>

    <link rel="stylesheet" href="{{ asset('css/components/layout.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    @stack('styles')
</head>

<body>
    <header>
        <nav>
            <a href="{{ url('/welcome') }}"><img src="{{ asset('storage/images/logo.png') }}" alt="Hotel Logo"></a>
            <ul>
                <li><a href="{{ url('/welcome') }}">Home</a></li>
                <li><a href="">About Us</a></li>
                <li class="dropdown">
                    <span>Support</span>
                    <div class="dropdown-content">
                        <a href="">FAQ</a>
                        <a href="">Feedback</a>
                    </div>
                </li>
                <li class="dropdown">
                    <span>Booking</span>
                    <div class="dropdown-content">
                        <a href="{{ url('booking/create') }}">Book Now</a>
                        <a href="{{ url('booking/list') }}">My Bookings</a>
                    </div>
                </li>
                <li class="icon dropdown">
                    <span class='fas fa-user'></span>
                    <div class="dropdown-content">
                        @guest
                            <a href="{{ route('login') }}">Login</a>
                            <a href="{{ route('signup.form') }}">Register</a>
                        @endguest

                        @auth
                            <a href="{{ route('profile.show') }}">View Profile</a>
                            <a href="{{ route('logout') }}"
                                onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    Logout
                            </a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                @csrf
                            </form>
                        @endauth

                    </div>
                </li>
                <li class="icon">
                    <a href="{{ url('/cart') }}" title="Booking Cart"><span class='fas fa-shopping-bag'></span></a>
                </li>
            </ul>
        </nav>
    </header>

    <main>
        @include('partials._cart_timer')
        {{ $slot }}
        <div id="snackbar-box">
            <div id="snackbar"></div>
        </div>
        @stack('scripts')
    </main>
    <footer>
        <div id="footer-text">
            <h2>Thank you for visiting Hotel Stripes Kuala Lumpur</h2>
            <span>We hope your stay with us was comfortable, enjoyable, and filled with memorable moments. At Hotel
                Stripes, we're committed to offering a unique blend of local charm and modern elegance, and we truly
                appreciate the opportunity to be part of your journey.
                Safe travels, and we look forward to welcoming you back again soon.</span>
            <ul>
                <li><a href=""><span class='fab fa-facebook-square' style="color: blue"></span></a></li>
                <li><a href=""><span class='fab fa-instagram' style="color: #E1306C"></span></a></li>
                <li><a href=""><span class='fab fa-whatsapp' style="color:green"></span></a></li>
                <li><a href=""><span class='fas fa-envelope'></span></a></li>
            </ul>
        </div>

        <div id="copyright-links">
            Copyright &copy;2025 &nbsp;&nbsp;Hotel Stripes Kuala Lumpur
            <ul>
                <li><a href="{{ url('/welcome') }}">Home</a></li>
                <li><a href="">About Us</a></li>
                <li><a href="">Support</a></li>
                <li><a href="{{ url('booking/create') }}">Booking</a></li>
            </ul>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://js.stripe.com/v3/"></script>
    <script src="{{ asset('js/components/layout.js') }}"></script>
</body>

</html>