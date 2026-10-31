{{-- Chin Jia Jun --}}

<x-layout>
    @push('styles')
        <link rel="stylesheet" href="{{ asset('css/booking/cart.css') }}">
    @endpush

    <div id="cart-container">
        <div id="cart-background">
            <video autoplay muted loop playsinline>
                <source src="{{ asset('storage/videos/cart_background.mp4') }}" type="video/mp4">
                Your browser does not support the video tag.
            </video>


            <div id="cart-content">
                <h1 id="title">My Booking Cart</h1>

                @if(count($cart) === 0)
                    <div id="empty-cart-message">
                        <p>Your cart is empty.</p>
                        <a href="{{ url('booking/create') }}" class="btn btn-primary">Back to Booking</a>
                    </div>

                @else
                    <div id="cart-items">
                        @foreach($cart as $index => $item)
                            <div class="cart-item" data-room-id="{{ $item['room_id'] }}">
                                <img id="room-image-{{ $index }}" alt="img">

                                <form action="/cart/remove" method="POST" class="remove-form">
                                    @csrf
                                    @method('DELETE')
                                    <input type="hidden" name="index" id="index" value="{{ $index }}">
                                    <button type="submit" class="remove-btn" title="Remove from cart">
                                        <i class="fas fa-trash-alt"></i>&nbsp;<i class="fas fa-minus-circle"></i>
                                    </button>
                                </form>

                                <div class="cart-item-desc">
                                    <h2 id="room-name-{{ $index }}"></h2>
                                    <h3><span id="room-price-{{ $index }}" class="price"></span> per night</h3>

                                    <div id="details">
                                        {{ $item['paxNo'] }} Pax &nbsp;|&nbsp;
                                        @php
                                            $start = \Carbon\Carbon::parse($item['startDate']);
                                            $end = \Carbon\Carbon::parse($item['endDate']);
                                            $days = $start->diffInDays($end); 
                                        @endphp

                                        {{ $start->format('d M Y') }} - {{ $end->format('d M Y') }}
                                        <span>({{$days}} Nights)</span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <a href="{{ url('/payment') }}" id="proceed-checkout-btn">Proceed to Checkout <i class="fas fa-play"></i></a>
                @endif
            </div>
        </div>
    </div>

    @if(session('success'))
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                Swal.fire("{{ session('success') }}", "", "success")
            });
        </script>

    @elseif(session('cart_expired'))
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                Swal.fire({
                    icon: "error",
                    title: "Oops...",
                    text: "{{ session('cart_expired') }}",
                });
            });
        </script>

    @elseif(session('cartError'))
        <script>
            showSnackbar("{{ session('cartError') }}");
        </script>
    @endif

    @push('scripts')
        <script src="{{ asset('js/booking/cart.js') }}"></script>
    @endpush
</x-layout>