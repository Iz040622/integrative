<?php
// LEOW SHI HAO 2314003 G3
namespace App\Services\Payments;

use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\PaymentIntent;
use Illuminate\Support\Facades\Session;
use App\Models\Payment;
use Carbon\Carbon;
use App\Events\PaymentSuccessful;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;

class StripePaymentStrategy implements PaymentStrategy
{
    public function __construct()
    {
        $apiKey = env('STRIPE_SECRET');
        if (!$apiKey) {
            Log::error('Stripe API key is missing');
            throw new \Exception('Stripe API configuration error');
        }
        Log::info('Stripe API Key initialized', ['key' => substr($apiKey, 0, 5) . '...']);
        Stripe::setApiKey($apiKey);
    }

    public function processPayment(Request $request): array
    {
        try {

            $request->validate([
                'payment_intent_id' => 'required|string',
                'total' => 'required|numeric|min:0.50',
            ]);

            $paymentIntentId = $request->input('payment_intent_id');
            $total = $request->input('total');

            Log::info('Processing Stripe payment', [
                'payment_intent_id' => $paymentIntentId,
                'total' => $total,
                'request_input' => $request->all()
            ]);


            if (!Session::has('subtotal') || !Session::has('tax')) {
                Log::error('Missing session data', ['session' => Session::all()]);
                throw new \Exception('Missing required session data for payment processing');
            }


            $paymentIntent = PaymentIntent::retrieve($paymentIntentId);

            Log::info('PaymentIntent retrieved', [
                'id' => $paymentIntent->id,
                'status' => $paymentIntent->status,
                'amount' => $paymentIntent->amount / 100
            ]);


            if ($paymentIntent->status === 'succeeded') {

            } elseif ($paymentIntent->status === 'requires_confirmation') {
                $paymentIntent = $paymentIntent->confirm();
                Log::info('PaymentIntent confirmed', ['id' => $paymentIntent->id, 'status' => $paymentIntent->status]);
            } else {
                Log::warning('Unexpected PaymentIntent status', ['status' => $paymentIntent->status]);
                throw new \Exception('PaymentIntent is in an unexpected state: ' . $paymentIntent->status);
            }

            if ($paymentIntent->status === 'succeeded') {
                $transactionId = 'TXN_' . uniqid();

                $payment = Payment::create([
                    'paymentDate' => Carbon::today(),
                    'paymentTime' => Carbon::now()->format('H:i:s'),
                    'paymentMethod' => 'Card',
                    'subTotal' => Session::get('subtotal'),
                    'totalAmount' => $total,
                    'invoiceUrl' => '/invoices/' . $transactionId . '.pdf',
                    'promo_id' => Session::get('promo_id'),
                ]);

                Log::info('Payment recorded in database', ['payment_id' => $payment->id, 'transaction_id' => $transactionId]);


                $bookings = Session::get('bookings', []);
                Session::put('receipt_data', [
                    'transaction_id' => $transactionId,
                    'subtotal' => Session::get('subtotal'),
                    'tax' => Session::get('tax'),
                    'discount' => Session::get('discount', 0),
                    'total' => $total,
                    'method' => 'Card',
                    'date' => Carbon::now()->format('d M Y, h:i A'),
                    'bookings' => $bookings,
                ]);

                Log::info('Receipt data set in Stripe strategy', ['receipt_data' => Session::get('receipt_data')]);

                $request = Request::create('/api/customer', 'GET');
                $response = Route::dispatch($request);
                $customer = json_decode($response->getContent(), true);
                event(new PaymentSuccessful($customer['id'], $payment->id));


                Session::forget([
                    'cart',
                    'transaction_id',
                    'payment_method',
                    'payment_total',
                    'subtotal',
                    'tax',
                    'discount',
                    'promo_id',
                    'promo_code',
                    'bookings'
                ]);

                return [
                    'success' => true,
                    'transaction_id' => $transactionId,
                ];
            }

            Log::warning('PaymentIntent not succeeded', ['status' => $paymentIntent->status]);
            return [
                'success' => false,
                'error' => 'Payment not completed: Invalid PaymentIntent status'
            ];
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Stripe validation error: ' . $e->getMessage(), ['input' => $request->all()]);
            $errorMessage = $e->errors()['payment_intent_id'][0] ?? $e->getMessage();
            return [
                'success' => false,
                'error' => 'Invalid input: ' . $errorMessage
            ];
        } catch (\Stripe\Exception\ApiErrorException $e) {
            Log::error('Stripe API error: ' . $e->getMessage(), ['input' => $request->all()]);
            return [
                'success' => false,
                'error' => 'Stripe error: ' . $e->getMessage()
            ];
        } catch (\Exception $e) {
            Log::error('Unexpected error in StripePaymentStrategy: ' . $e->getMessage(), ['input' => $request->all()]);
            return [
                'success' => false,
                'error' => 'Payment processing failed: ' . $e->getMessage()
            ];
        }
    }
}