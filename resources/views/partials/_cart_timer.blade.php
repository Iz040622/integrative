{{-- Chin Jia Jun --}}

<style>
    #cart-timer-container {
        position: fixed;
        bottom: 40px;
        left: 20px;
        color: white;
        background-color: black;
        padding: 10px 15px;
        border-radius: 5px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
        z-index: 1000;
        display: flex;
        align-items: center;
        gap: 10px;
        text-decoration: none;
        transition: background-color 0.2s ease;
    }

    #cart-timer-container:hover {
        background-color: rgb(45, 45, 45);
        cursor: pointer;
    }

    .spinning {
        animation: spin 5s linear infinite;
        color: white;
        font-size: 24px;
    }

    @keyframes spin {
        from {
            transform: rotate(0deg);
        }

        to {
            transform: rotate(360deg);
        }
    }

    #cart-timer {
        font-weight: bold;
        font-size: 18px;
        color: red;
    }
</style>

@if(isset($cartExpiresAt))
    <a href="{{ url('/cart') }}" id="cart-timer-container">
        <i class="fas fa-hourglass-half spinning"></i>
        <span>Cart expires in <span id="cart-timer">15:00</span></span>
    </a>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const cartTimerElement = document.getElementById('cart-timer');
            let cartExpiresAt = new Date("{{ $cartExpiresAt }}").getTime();

            function updateCartTimer() {
                let now = new Date().getTime();
                let distance = cartExpiresAt - now;

                if (distance <= 0) {
                    cartTimerElement.textContent = "00:00";
                    clearInterval(timerInterval);
                    location.reload();
                    return;
                }

                let minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                let seconds = Math.floor((distance % (1000 * 60)) / 1000);

                cartTimerElement.textContent = `${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;
            }

            updateCartTimer();
            const timerInterval = setInterval(updateCartTimer, 1000);
        });
    </script>
@endif