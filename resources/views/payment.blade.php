<!-- LEOW SHI HAO 2314003 G3 -->
<x-layout>
    @push('styles')
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="{{ asset('css/payment.css') }}">
    @endpush
    <div class="payment-container">
        <h2 class="text-center mb-4">Hotel Payment</h2>

        <!-- Countdown Timer -->
        <div class="text-center mb-4">
            <span class="timer" id="countdown">15:00</span>
            <small class="text-muted d-block">Time remaining to complete payment</small>
        </div>

        <!-- Progress Bar -->
        <div class="progress-container">
            <div class="progress-bar-bg"></div>
            <div class="progress-bar" id="progress-bar" style="width: 33%;"></div>
            <div class="progress-step active" data-step="1">
                <div class="progress-step-circle">1</div>
                <div class="progress-step-label">Review</div>
            </div>
            <div class="progress-step" data-step="2">
                <div class="progress-step-circle">2</div>
                <div class="progress-step-label">Payment</div>
            </div>
            <div class="progress-step" data-step="3">
                <div class="progress-step-circle">3</div>
                <div class="progress-step-label">Complete</div>
            </div>
        </div>

        <!-- Customer Room Checklist -->
        <div class="mb-4">
            <h4>Booking Summary</h4>
            @foreach ($bookings as $booking)
                <div class="checklist-item">
                    <span>{{ $booking['name'] }} - {{ $booking['room_type'] }}</span>
                    <span>RM {{ number_format($booking['price'], 2) }}</span>
                </div>
            @endforeach
            <div class="checklist-total">
                <div>Subtotal: RM <span id="subtotal-price">{{ number_format($subtotal, 2) }}</span></div>
                <div>Tax (6%): RM <span id="tax-price">{{ number_format($tax, 2) }}</span></div>
                <div>Discount: RM <span id="discount-price">0.00</span></div>
                <div>Total: RM <span id="total-price">{{ number_format($total, 2) }}</span></div>
            </div>
        </div>

        <!-- Promotion Code -->
        <div class="promo-section">
            <h4>Apply Promotion Code</h4>
            <div class="input-group">
                <input type="text" class="form-control" id="promo-code" placeholder="Enter promo code">
                <button class="btn btn-primary" id="apply-promo">Apply</button>
            </div>
            <small class="promo-error text-danger" id="promo-error" style="display: none;">Invalid promo code</small>
        </div>

        <!-- Payment Method Selection -->
        <div class="mb-4">
            <h4>Select Payment Method</h4>
            <div class="payment-method" data-method="tng">TNG (Touch 'n Go)</div>
            <div class="payment-method" data-method="card">Card Pay</div>
        </div>

        <!-- Payment Pages -->
        <div id="tng-page" class="payment-page" style="display: none;">
            <h4>TNG Payment</h4>
            <form id="tng-form" action="{{ route('payment.tng') }}" method="POST">
                @csrf
                <input type="hidden" name="total" id="tng-total" value="{{ $total }}">
                <div class="mb-3 text-center">
                    <p>Please scan the QR code below to make your TNG payment:</p>
                    <img src="{{ asset('images/tng.jpeg') }}" alt="TNG QR Code" style="max-width: 200px; height: auto;">
                </div>
                <div id="tng-success" class="alert alert-success" style="display: none;">
                    Payment successful! Redirecting to your receipt...
                </div>
                <div id="tng-errors" class="text-danger" role="alert"></div>
                <div class="tng-submit">
                    <button type="submit" class="btn btn-primary" id="tng-button">Confirm Payment</button>
                </div>
                
            </form>
        </div>
        <div id="card-page" class="payment-page" style="display: none;">
            <h4>Card Payment</h4>
            <form id="payment-form" method="POST" action="{{ route('payment.card') }}">
                @csrf
                <input type="hidden" name="total" id="card-total" value="{{ $total }}">
                <div class="mb-3">
                    <label for="card-element" class="form-label">Card Details</label>
                    <div id="card-element"></div>
                    <div id="card-errors" class="text-danger" role="alert"></div>
                </div>
                <div class="card-submit">
                    <button type="submit" class="btn btn-primary" id="card-button">Pay Now</button>
                </div>                
                
            </form>
        </div>

        <!-- Timeout Modal -->
        <div class="modal fade" id="timeoutModal" tabindex="-1" aria-labelledby="timeoutModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="timeoutModalLabel">Session Expired</h5>
                    </div>
                    <div class="modal-body">
                        Your time is up. You will be redirected to the homepage.
                    </div>
                </div>
            </div>
        </div>

        <!-- Success Modal -->
        <div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="successModalLabel">Payment Successful</h5>
                    </div>
                    <div class="modal-body">
                        Payment Completed Successfully! Redirecting to your receipt...
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://js.stripe.com/v3/"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
        <script>
            window.paymentConfig = {
                stripeKey: '{{ env('STRIPE_KEY') }}',
                clientSecret: '{{ $clientSecret }}',
                promoRoute: '{{ route('payment.promo') }}',
                tngRoute: '{{ route('payment.tng') }}',
                cardRoute: '{{ route('payment.card') }}',
                updateIntentRoute: '{{ route('payment.update_intent') }}',
                csrfToken: '{{ csrf_token() }}',
                isPaymentCompleted: {{ session('transaction_id') ? 'true' : 'false' }}
            };
        </script>
        <script src="{{ asset('js/payment.js') }}"></script>
    @endpush
</x-layout>