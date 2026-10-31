// LEOW SHI HAO 2314003 G3
try {
    const stripe = Stripe(window.paymentConfig.stripeKey);
    const elements = stripe.elements();
    const cardElement = elements.create('card', {
        style: {
            base: {
                fontSize: '16px',
                color: '#32325d',
                '::placeholder': {
                    color: '#aab7c4',
                },
            },
            invalid: {
                color: '#dc3545',
            },
        },
        hidePostalCode: false,
    });
    cardElement.mount('#card-element');

    cardElement.on('change', (event) => {
        console.log('Card element change:', event);
        const cardErrors = document.getElementById('card-errors');
        if (event.error) {
            cardErrors.textContent = event.error.message;
        } else {
            cardErrors.textContent = '';
        }
    });

    // Countdown Timer
    let timeLeft = 15 * 60; // 15 minutes in seconds
    const countdown = document.getElementById('countdown');
    let timerInterval = setInterval(() => {
        let minutes = Math.floor(timeLeft / 60);
        let seconds = timeLeft % 60;
        countdown.textContent = `${minutes}:${seconds < 10 ? '0' : ''}${seconds}`;
        timeLeft--;
        if (timeLeft < 0) {
            clearInterval(timerInterval);
            countdown.textContent = "Your time is up";
            document.getElementById('card-button').disabled = true;
            document.getElementById('tng-button').disabled = true;
            const timeoutModal = new bootstrap.Modal(document.getElementById('timeoutModal'), {
                backdrop: 'static',
                keyboard: false
            });
            timeoutModal.show();
            setTimeout(() => {
                window.location.href = '/booking/create';
            }, 2000);
        }
    }, 1000);

    // Reset Form Function
    function resetPaymentForm() {
        // Clear form inputs
        document.getElementById('promo-code').value = '';
        cardElement.clear();
        
        // Reset error messages
        document.getElementById('promo-error').style.display = 'none';
        document.getElementById('tng-errors').textContent = '';
        document.getElementById('card-errors').textContent = '';
        
        // Hide payment pages and success message
        document.querySelectorAll('.payment-page').forEach(page => page.style.display = 'none');
        document.getElementById('tng-success').style.display = 'none';
        document.querySelectorAll('.payment-method').forEach(method => method.classList.remove('active'));
        
        // Reset progress bar
        updateProgressBar(1, 33);
        
        // Reset timer
        timeLeft = 15 * 60;
        clearInterval(timerInterval);
        timerInterval = setInterval(() => {
            let minutes = Math.floor(timeLeft / 60);
            let seconds = timeLeft % 60;
            countdown.textContent = `${minutes}:${seconds < 10 ? '0' : ''}${seconds}`;
            timeLeft--;
            if (timeLeft < 0) {
                clearInterval(timerInterval);
                countdown.textContent = "Your time is up";
                document.getElementById('card-button').disabled = true;
                document.getElementById('tng-button').disabled = true;
                const timeoutModal = new bootstrap.Modal(document.getElementById('timeoutModal'), {
                    backdrop: 'static',
                    keyboard: false
                });
                timeoutModal.show();
                setTimeout(() => {
                    window.location.href = '/cart';
                }, 2000);
            }
        }, 1000);
        
        // Enable buttons
        document.getElementById('card-button').disabled = false;
        document.getElementById('tng-button').disabled = false;
    }

    // Promotion Code Handling
    document.getElementById('apply-promo').addEventListener('click', () => {
        const promoCode = document.getElementById('promo-code').value.trim();
        const promoError = document.getElementById('promo-error');
        const subtotalPriceEl = document.getElementById('subtotal-price');
        const taxPriceEl = document.getElementById('tax-price');
        const discountPriceEl = document.getElementById('discount-price');
        const totalPriceEl = document.getElementById('total-price');
        const paymentInputs = [document.getElementById('tng-total'), document.getElementById('card-total')];

        fetch(window.paymentConfig.promoRoute, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': window.paymentConfig.csrfToken,
            },
            body: JSON.stringify({ promo_code: promoCode, subtotal: parseFloat(subtotalPriceEl.textContent) }),
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                subtotalPriceEl.textContent = data.subtotal;
                taxPriceEl.textContent = data.tax;
                discountPriceEl.textContent = data.discount;
                totalPriceEl.textContent = data.total;
                paymentInputs.forEach(input => input.value = data.total);
                promoError.style.display = 'none';
                if (document.querySelector('.payment-method.active')?.dataset.method === 'card') {
                    fetch(window.paymentConfig.updateIntentRoute, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': window.paymentConfig.csrfToken,
                        },
                        body: JSON.stringify({ total: data.total }),
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.client_secret) {
                            window.paymentConfig.clientSecret = data.client_secret;
                            console.log('Updated clientSecret:', data.client_secret);
                        } else {
                            console.error('Failed to update clientSecret:', data);
                            promoError.textContent = 'Error updating payment intent. Please try again.';
                            promoError.style.display = 'block';
                        }
                    })
                    .catch(error => {
                        console.error('Error updating payment intent:', error);
                        promoError.textContent = 'Error updating payment intent. Please try again.';
                        promoError.style.display = 'block';
                    });
                }
            } else {
                promoError.textContent = data.error;
                promoError.style.display = 'block';
            }
        })
        .catch(() => {
            promoError.textContent = 'Error applying promo code';
            promoError.style.display = 'block';
        });
    });

    // Payment Method Selection
    const paymentMethods = document.querySelectorAll('.payment-method');
    const paymentPages = document.querySelectorAll('.payment-page');
    paymentMethods.forEach(method => {
        method.addEventListener('click', () => {
            paymentMethods.forEach(m => m.classList.remove('active'));
            method.classList.add('active');
            paymentPages.forEach(page => page.style.display = 'none');
            const targetPage = document.getElementById(`${method.dataset.method}-page`);
            targetPage.style.display = 'block';
            updateProgressBar(2, 66);
            targetPage.scrollIntoView({ behavior: 'smooth', block: 'start' });
        });
    });

    // TNG Payment Handling
    const tngForm = document.getElementById('tng-form');
    const tngButton = document.getElementById('tng-button');
    const tngErrors = document.getElementById('tng-errors');
    const tngSuccess = document.getElementById('tng-success');
    tngForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        tngButton.disabled = true;
        tngErrors.textContent = '';
        tngSuccess.style.display = 'none';

        const total = document.getElementById('tng-total').value;

        const payload = {
            payment_method: 'tng',
            total: total
        };
        console.log('Sending TNG payload to backend:', payload);

        try {
            const response = await fetch(window.paymentConfig.tngRoute, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': window.paymentConfig.csrfToken,
                },
                body: JSON.stringify(payload),
            });
            
            const data = await response.json();
            console.log('TNG response:', data);

            if (data.success) {
                updateProgressBar(3, 100);
                clearInterval(timerInterval);
                countdown.textContent = "Payment Completed";
                tngSuccess.style.display = 'block';
                tngSuccess.scrollIntoView({ behavior: 'smooth', block: 'start' });
                setTimeout(() => {
                    tngSuccess.style.display = 'none';
                    resetPaymentForm();
                    window.location.href = data.redirect;
                }, 3000);
            } else {
                tngErrors.textContent = data.error || 'TNG payment processing failed';
                tngButton.disabled = false;
            }
        } catch (error) {
            console.error('Error processing TNG payment:', error);
            tngErrors.textContent = 'Error processing TNG payment. Please try again.';
            tngButton.disabled = false;
        }
    });

    // Stripe Payment Handling
    const paymentForm = document.getElementById('payment-form');
    const cardButton = document.getElementById('card-button');
    const cardErrors = document.getElementById('card-errors');
    paymentForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        cardButton.disabled = true;
        cardErrors.textContent = '';

        if (!window.paymentConfig.clientSecret) {
            console.error('Missing clientSecret');
            cardErrors.textContent = 'Error: Payment configuration is invalid. Please refresh the page and try again.';
            cardButton.disabled = false;
            return;
        }

        console.log('Attempting to confirm card payment with clientSecret:', window.paymentConfig.clientSecret);

        const { paymentIntent, error } = await stripe.confirmCardPayment(window.paymentConfig.clientSecret, {
            payment_method: {
                card: cardElement,
            },
        });

        if (error) {
            console.error('Stripe confirmCardPayment error:', error);
            cardErrors.textContent = error.message || 'Error processing card payment. Please try again.';
            cardButton.disabled = false;
            return;
        }

        if (!paymentIntent || !paymentIntent.id) {
            console.error('Invalid paymentIntent:', paymentIntent);
            cardErrors.textContent = 'Error: Invalid payment intent received. Please try again.';
            cardButton.disabled = false;
            return;
        }

        console.log('PaymentIntent received:', paymentIntent);

        const payload = {
            payment_method: 'card', // Explicit for consistency
            payment_intent_id: paymentIntent.id,
            total: document.getElementById('card-total').value,
        };
        console.log('Sending Stripe payload to backend:', payload);

        try {
            const response = await fetch(window.paymentConfig.cardRoute, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': window.paymentConfig.csrfToken,
                },
                body: JSON.stringify(payload),
            });

            const data = await response.json();
            if (data.success) {
                updateProgressBar(3, 100);
                clearInterval(timerInterval);
                countdown.textContent = "Payment Completed";
                const successModal = new bootstrap.Modal(document.getElementById('successModal'), {
                    backdrop: 'static',
                    keyboard: false
                });
                successModal.show();
                setTimeout(() => {
                    successModal.hide();
                    resetPaymentForm();
                    window.location.href = data.redirect;
                }, 3000);
            } else {
                console.error('Backend processing error:', data);
                cardErrors.textContent = data.error || 'Payment processing failed. Please try again.';
                cardButton.disabled = false;
            }
        } catch (error) {
            console.error('Error sending payment to backend:', error);
            cardErrors.textContent = 'Error communicating with server. Please try again.';
            cardButton.disabled = false;
        }
    });

    // Progress Bar Update
    function updateProgressBar(step, percentage) {
        const progressBar = document.getElementById('progress-bar');
        const steps = document.querySelectorAll('.progress-step');
        steps.forEach(s => s.classList.remove('active', 'completed'));
        for (let i = 0; i < step; i++) {
            steps[i].classList.add('completed');
        }
        steps[step - 1].classList.add('active');
        progressBar.style.width = `${percentage}%`;
    }

    // Handle page load if payment is already completed
    if (window.paymentConfig.isPaymentCompleted) {
        const successModal = new bootstrap.Modal(document.getElementById('successModal'), {
            backdrop: 'static',
            keyboard: false
        });
        successModal.show();
        setTimeout(() => {
            successModal.hide();
            resetPaymentForm();
            window.location.href = "{{ route('payment.receipt') }}";
        }, 3000);
    }
} catch (error) {
    console.error('Initialization error in payment.js:', error);
    document.getElementById('card-errors').textContent = 'Error initializing payment form. Please refresh the page.';
}