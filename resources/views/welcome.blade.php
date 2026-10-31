<x-layout>
    @include('partials._book')

    @push('styles')
        <link rel="stylesheet" href="{{ asset('css/welcome.css') }}">
    @endpush

    <div id="slideshow-box">
        @php
            $images = [
                'logoview' => 'Hotel Logo View',
                'sceneview' => 'Hotel Scene View',
                'diningview' => 'Hotel Dining Area',
                'livingroom' => 'Hotel Living Room',
                'roomview' => 'Hotel Room Interior'
            ];
        @endphp

        @foreach ($images as $filename => $alt)
            <img src="{{ asset('storage/images/' . $filename . '.png') }}"
                class="slideshow {{ $loop->index === 0 ? 'active' : '' }}" alt="{{ $alt }}">
        @endforeach

        <div id="dots">
            @foreach ($images as $image)
                <span class="dot" style="{{ $loop->index === 0 ? 'background-color: white;' : '' }}"></span>
            @endforeach
        </div>
    </div>

    <div id="intro-message-box" class="effect">
        <h3>WELCOME TO HOTEL STRIPES KUALA LUMPUR, AUTOGRAPH COLLECTION</h3>
        <h1>A YTL LUXURY HOTEL ON JALAN KAMUNTING, <br />MALAYSIA</h1>
        <p>Stay right in Kuala Lumpur's business and social hub, near some of the city's top restaurants, art galleries,
            shops, and legendary landmarks. Discover KL's most creative and engaged at our hotel near downtown <br>Kuala
            Lumpur neighborhood at our modern Kuala Lumpur hotel.</p>
        <hr />
    </div>

    <div id="room-showcase" class="effect">
        <h1>Experience our Stripes Room</h1>

        <div id="room-box">
            <div id="rooms">
                
            </div>
        </div>

        <div id="room-box-arrow">
            <span id="prevBtn" onclick="previousRoom()">⟨</span>
            <span id="nextBtn" onclick="nextRoom()">⟩</span>
        </div>
    </div>

    <div id="scenedeco">
    </div>

    <div id="facility-service" class="effect">
        <h1>Facilities & Services</h1>
        <table>
            <tr>
                <td><span class='fas fa-leaf'></span> Sustainability</td>
                <td><span class='fas fa-utensils'></span> Restaurant</td>
                <td><span class='fas fa-dumbbell'></span> Fitness Center</td>
                <td><span class='fas fa-swimmer'></span> Outdoor Pool</td>
            </tr>
            <tr>
                <td><span class='far fa-calendar-alt'></span> Meeting Space</td>
                <td><span class='fas fa-wifi'></span> Free Wifi</td>
                <td><span class='fas fa-phone-alt'></span> Wake Up Calls</td>
                <td><span class='fas fa-mobile-alt'></span> Digital Check-In</td>
            </tr>
            <tr>
                <td><span class='fas fa-check'></span> Daily Housekeeping</td>
                <td><span class='fas fa-check'></span> Turndown Service</td>
                <td><span class='fas fa-check'></span> Service Request</td>
            </tr>
        </table>
        <hr />
    </div>

    <div id="aboutus-section" class="effect">
        <div id="aboutus-img">
            <iframe
                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3983.7627608491703!2d101.69799777497123!3d3.1571349968182476!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x31cc482eba08fde9%3A0x6341088fbb040af4!2sHotel%20Stripes%20Kuala%20Lumpur%2C%20Autograph%20Collection!5e0!3m2!1sen!2smy!4v1752396341031!5m2!1sen!2smy"
                width="500" height="375" style="border:0;" allowfullscreen="" loading="lazy"
                referrerpolicy="no-referrer-when-downgrade"></iframe>
        </div>
        <div id="aboutus-text">
            <h2>Experience Kuala Lumpur's energy and rich culture</h2>
            <span>Hotel Stripes Kuala Lumpur is proud to be a member of Autograph Collection Hotels, an evolving
                ensemble of strikingly independent hotels. Each destination has been selected for its quality, bold
                originality, rich character and uncommon details. From near to far, iconic to historic, the result is an
                array of properties that is nothing less than unique, nothing short of collectively exceptional.
                As an Autograph Collection, Hotel Stripes Kuala Lumpur features a distinctive vibe and character with a
                different perspective on the local scene mixed with a bit of edgy style and adventure.</span>
            <a href="">Learn More</a>
        </div>
    </div>

    @if(session('error'))
        <script>
            showSnackbar("{{ session('error') }}");
        </script>
    @endif

    @push('scripts')
        <script src="{{ asset('js/welcome.js') }}"></script>
    @endpush

</x-layout>