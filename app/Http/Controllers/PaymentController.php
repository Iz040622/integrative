<?php
// LEOW SHI HAO 2314003 G3
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\PaymentIntent;
use Illuminate\Support\Facades\Session;
use App\Models\RoomType;
use App\Models\Room;
use App\Models\Booking;
use App\Models\PromoCode;
use App\Models\Payment;
use App\Models\Tng;
use App\Models\Card;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Services\Payments\PaymentStrategy;
use App\Services\Payments\StripePaymentStrategy;
use App\Services\Payments\TngPaymentStrategy;
use App\Events\PaymentSuccessful;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;

class PaymentController extends Controller
{
    protected $paymentStrategy;

    public function __construct(PaymentStrategy $paymentStrategy = null)
    {

        $this->paymentStrategy = $paymentStrategy ?? new StripePaymentStrategy();
    }

    public function showPaymentForm(Request $request)
    {
        $request = Request::create('/api/cartItems', 'GET');
        $response = Route::dispatch($request);
        $data = json_decode($response->getContent(), true);

        $cart = $data['cart'] ?? [];
        if (empty($cart)) {
            return redirect('/cart')->with('error', 'Your cart is empty.');
        }

        $bookings = [];
        $request = Request::create('/api/customer', 'GET');
        $response = Route::dispatch($request);
        $customer = json_decode($response->getContent(), true);
        $userId = $customer['id'];
        $userName = $customer['name'];

        foreach ($cart as $index => $item) {
            $roomType = Http::get(config('app.url') . '/api/room/' . $item['room_id'] . '/roomType')->json();
            if (!$roomType) {
                continue;
            }
            $startDate = Carbon::parse($item['startDate']);
            $endDate = Carbon::parse($item['endDate']);
            $nights = max(1, $endDate->diffInDays($startDate));
            $price = $nights * $roomType['price'] * $item['paxNo'];

            $bookings[] = [
                'id' => $index + 1,
                'user_id' => $userId,
                'startDate' => $startDate->toDateString(),
                'endDate' => $endDate->toDateString(),
                'bookingDate' => Carbon::today()->toDateString(),
                'number_of_pax' => $item['paxNo'],
                'room_id' => $item['room_id'],
                'name' => $userName,
                'room_type' => $roomType['name'],
                'price' => $price,
            ];
        }

        if (empty($bookings)) {
            Session::forget('cart');
            return redirect('/cart')->with('error', 'No valid bookings found.');
        }

        $subtotal = array_sum(array_column($bookings, 'price'));
        $tax = $subtotal * 0.06;
        $total = $subtotal + $tax;


        Stripe::setApiKey(env('STRIPE_SECRET'));


        if ($total < 0.50) {
            return redirect('/cart')->with('error', 'Total amount must be at least RM 0.50 for card payments.');
        }


        $paymentIntent = PaymentIntent::create([
            'amount' => (int) ($total * 100),
            'currency' => 'myr',
            'payment_method_types' => ['card'],
        ]);


        Session::put('subtotal', $subtotal);
        Session::put('tax', $tax);
        Session::put('payment_total', $total);
        Session::put('bookings', $bookings);

        return view('payment', [
            'bookings' => $bookings,
            'subtotal' => $subtotal,
            'tax' => $tax,
            'total' => $total,
            'clientSecret' => $paymentIntent->client_secret,
            'transaction_id' => Session::get('transaction_id'),
            'method' => Session::get('payment_method'),
        ]);
    }

    public function applyPromo(Request $request)
    {
        $promoCode = $request->input('promo_code');
        $subtotal = $request->input('subtotal');


        $promoResponse = Http::get(config('app.url') . '/api/promo/' . $promoCode)->json();
        $promo = $promoResponse['promo'] ?? null;

        if ($promo) {
            $discount = $promo['discount'];
            $tax = $subtotal * 0.06;
            $total = $subtotal + $tax - $discount;


            if ($total < 0.50) {
                return response()->json(['success' => false, 'error' => 'Total amount after discount must be at least RM 0.50']);
            }

            Session::put('payment_total', $total);
            Session::put('discount', $discount);
            Session::put('promo_id', $promo['id']);
            return response()->json([
                'success' => true,
                'subtotal' => number_format($subtotal, 2),
                'tax' => number_format($tax, 2),
                'discount' => number_format($discount, 2),
                'total' => number_format($total, 2),
            ]);
        }

        return response()->json(['success' => false, 'error' => 'Invalid or expired promo code']);
    }

    public function updatePaymentIntent(Request $request)
    {
        Stripe::setApiKey(env('STRIPE_SECRET'));
        $total = $request->input('total');


        if ($total < 0.50) {
            return response()->json(['success' => false, 'error' => 'Total amount must be at least RM 0.50']);
        }


        $paymentIntent = PaymentIntent::create([
            'amount' => (int) ($total * 100),
            'currency' => 'myr',
            'payment_method_types' => ['card'],
        ]);

        return response()->json(['client_secret' => $paymentIntent->client_secret]);
    }

    public function processPayment(Request $request)
    {
        $paymentMethod = trim(strtolower($request->input('payment_method', 'card'))); // Trim and lowercase for robustness
        Log::info('Payment processing started', [
            'payment_method' => $paymentMethod,
            'request_input' => $request->all()
        ]);


        if ($paymentMethod === 'tng') {
            $this->paymentStrategy = new TngPaymentStrategy();
            Log::info('TNG strategy selected');
        } else {
            $this->paymentStrategy = new StripePaymentStrategy();
            Log::info('Stripe strategy selected');
        }


        $result = $this->paymentStrategy->processPayment($request);

        if ($result['success']) {
            return response()->json([
                'success' => true,
                'transaction_id' => $result['transaction_id'],
                'redirect' => route('payment.receipt')
            ]);
        }

        return response()->json([
            'success' => false,
            'error' => $result['error']
        ]);
    }

    public function showReceipt(Request $request)
    {
        $receiptData = Session::get('receipt_data', [
            'transaction_id' => 'N/A',
            'subtotal' => 0.00,
            'tax' => 0.00,
            'discount' => 0.00,
            'total' => 0.00,
            'method' => 'N/A',
            'date' => Carbon::today()->format('d M Y'),
            'bookings' => [],
        ]);

        if (empty($receiptData)) {
            return redirect('/cart')->with('error', 'No receipt data available.');
        }


        Session::forget('receipt_data');

        return view('receipt', [
            'transaction_id' => $receiptData['transaction_id'],
            'subtotal' => $receiptData['subtotal'],
            'tax' => $receiptData['tax'],
            'discount' => $receiptData['discount'],
            'total' => $receiptData['total'],
            'method' => $receiptData['method'],
            'date' => $receiptData['date'],
            'bookings' => $receiptData['bookings'] ?? [],
        ]);
    }

    public function apiGetPayment()
    {
        try {
            $payment = Payment::all();
            return response()->json($payment);

        } catch (\Exception $e) {
            return response()->json([
                'error' => true,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}