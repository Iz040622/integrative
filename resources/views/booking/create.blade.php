{{-- Chin Jia Jun --}}

<x-layout>
    @push('styles')
        <link rel="stylesheet" href="{{ asset('css/booking/create.css') }}">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    @endpush

    @if($errors->any())
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                showSnackbar(@json($errors->first()));
            });
        </script>
    @endif

    <video id="bookingscene" autoplay loop muted>
        <source src="{{ asset('storage/videos/bookingscene.mp4') }}" type="video/mp4">
        Your browser does not support the video tag.
    </video>

    <form id="bookingform" method="GET">
        @csrf
        <div class="booking-input">
            <p>
                <span class='fas fa-user'></span>
                NUMBER OF PAX
            </p>

            <p class="pax" style="margin-right: 50px;">
                <label for="adultno">ADULT: &nbsp;&nbsp;</label>
                <button type="button" id="minus-adult">&minus;</button>
                <input type="number" id="adultno" name="adultno" value="1" min="1" max="8" readonly>
                <button type="button" id="plus-adult">&plus;</button>
            </p>

            <p class="pax">
                <label for="childno">CHILD: &nbsp;&nbsp;</label>
                <button type="button" id="minus-child">&minus;</button>
                <input type="number" id="childno" name="childno" value="0" min="0" max="8" readonly>
                <button type="button" id="plus-child">&plus;</button>
            </p>
        </div>

        <div class="booking-input">
            <p>
                <label for="dates">
                    <span class='far fa-calendar-alt'></span>
                    DATES &#40;<span id="nights">1</span> NIGHT&#41;
                </label>
            </p>
            <p>
                <input type="text" id="dates" name="dates" class="datepicker" placeholder="Please select a date range"
                    required>
            </p>
        </div>

        <div id="submitbtn">
            <button type="submit">Find Room</button>
        </div>
    </form>

    <div id="no-room-msg">
        <h2>Sorry, no room is available. Please try again.</h2>
    </div>

    <div id="room-list">
    </div>

    <div id="room-detail-modal">
        <div>
            <span id="close-detail" onclick="closeDetailModal()">&times;</span>
            <img id="detail-img">

            <table>
                <tr>
                    <td rowspan="2">
                        <h1 id="room-detail-name"></h1>
                    </td>
                    <td>
                        <h2>Pax: <span id="room-detail-pax"></span> <span class='fas fa-user'></span></h2>
                    </td>
                </tr>
                <tr>
                    <td>
                        <h2>RM<span id="room-detail-price"></span> per night</h2>
                    </td>
                </tr>
            </table>

            <div id="room-detail-desc"></div>

            <div id="detail-amenities">
                <span></span>
                <table>
                </table>
            </div>

            <form id="detail-add-to-cart-form" action="/cart/add" method="POST">
                @csrf
                <input type="hidden" name="startDate" id="startDate" value="">
                <input type="hidden" name="endDate" id="endDate" value="">
                <input type="hidden" name="paxNo" id="paxNo" value="">
                <input type="hidden" name="room_id" id="room_id" value="">
                <button type="submit">Book Now</button>
            </form>
        </div>
    </div>

    @push('scripts')
        <script>
            const csrfToken = '{{ csrf_token() }}';
        </script>
        <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
        <script src="{{ asset('js/booking/create.js') }}"></script>
    @endpush
</x-layout>