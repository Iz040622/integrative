{{-- Chin Jia Jun --}}

<x-layout>
    @push('styles')
        <link rel="stylesheet" href="{{ asset('css/booking/list.css') }}">
    @endpush

    <div id="booking-list-container">
        <h1>My Bookings</h1>

        @if(count($bookings) === 0)
            <div id="empty-booking-message">
                <p>No bookings available.</p>
                <a href="{{ url('booking/create') }}" class="btn btn-primary">Book Now</a>
            </div>

        @else
            @foreach($bookings as $booking)
                <div class="booking-card">
                    <h2><i class="far fa-calendar-alt"></i> Booking Date: {{ $booking->bookingDate->format('d M Y') }}</h2>

                    @foreach($booking->bookingRooms as $index => $bookingRoom)
                        <div class="booking-room" data-room-id="{{ $bookingRoom->room_id }}"
                            data-checkin="{{ $bookingRoom->checkInDate }}" data-checkout="{{ $bookingRoom->checkOutDate }}"
                            data-booking-id="{{ $booking->id }}" data-index="{{ $index }}">
                            <img id="room-image-{{ $booking->id }}-{{ $index }}" alt="Room Image">

                            @php
                                $checkIn = \Carbon\Carbon::parse($bookingRoom->checkInDate);
                                $checkOut = \Carbon\Carbon::parse($bookingRoom->checkOutDate);
                                $nights = $checkIn->diffInDays($checkOut);
                            @endphp

                            <table>
                                <tr>
                                    <td id="room-name-{{ $booking->id }}-{{ $index }}" class="room-name"></td>
                                    <td>Room No: <span id="room-no-{{ $booking->id }}-{{ $index }}"></span></td>
                                    <td>Status: <span
                                            class="{{ strtolower($bookingRoom->status) }}">{{ $bookingRoom->status }}</span></td>
                                </tr>
                                <tr>
                                    <td>{{ $checkIn->format('d/m/Y') }} - {{ $checkOut->format('d/m/Y') }} ({{ $nights }}
                                        night{{ $nights > 1 ? 's' : '' }})</td>
                                    <td>Pax: {{ $bookingRoom->pax }}</td>
                                    <td id="room-price-{{ $booking->id }}-{{ $index }}"></td>
                                </tr>
                            </table>

                            @if($bookingRoom->status === 'Pending')
                                <form action="/bookingRoom/{{ $bookingRoom->id }}/cancel" method="POST" class="cancel-form">
                                    @csrf
                                    <input type="hidden" name="status" value="Cancelled">
                                    <button type="submit" class="cancel-btn" title="Cancel Booking">
                                        <i class="fas fa-times-circle"></i>&nbsp;Cancel Booking
                                    </button>
                            @endif
                        </div>

                        @if (!$loop->last)
                            <hr>
                        @endif
                    @endforeach
                </div>
            @endforeach
        @endif
    </div>

    @if(session('success'))
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                Swal.fire("{{ session('success') }}", "", "success")
            });
        </script>
        
    @elseif(session('error'))
        <script>
            showSnackbar("{{ session('error') }}");
        </script>
    @endif

    @push('scripts')
        <script src="{{ asset('js/booking/list.js') }}"></script>
    @endpush
</x-layout>