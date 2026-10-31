<?php
// LEOW SHI HAO 2314003 G3
namespace App\Services\Payments;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Events\PaymentSuccessful;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use App\Models\Payment;
use Carbon\Carbon;

class TngPaymentStrategy implements PaymentStrategy
{
    public function processPayment(Request $request): array
    {
        try {

            Log::info('TNG payment processing started', ['input' => $request->all()]);


            $request->validate([
                'total' => 'required|numeric|min:0.50',
            ]);

            $total = $request->input('total');
            Log::info('TNG payment validated', ['total' => $total]);


            $transactionId = 'TXN_' . uniqid();


            $payment = Payment::create([
                'paymentDate' => Carbon::today(),
                'paymentTime' => Carbon::now()->format('H:i:s'),
                'paymentMethod' => 'TNG',
                'subTotal' => Session::get('subtotal'),
                'totalAmount' => $total,
                'invoiceUrl' => '/invoices/' . $transactionId . '.pdf',
                'promo_id' => Session::get('promo_id'),
            ]);


            $bookings = Session::get('bookings', []);
            Session::put('receipt_data', [
                'transaction_id' => $transactionId,
                'subtotal' => Session::get('subtotal'),
                'tax' => Session::get('tax'),
                'discount' => Session::get('discount', 0),
                'total' => $total,
                'method' => 'TNG',
                'date' => Carbon::now()->format('d M Y, h:i A'),
                'bookings' => $bookings,
            ]);

            Log::info('Receipt data set in TNG strategy', ['receipt_data' => Session::get('receipt_data')]);

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

            Log::info('TNG payment successful', ['transaction_id' => $transactionId]);

            return [
                'success' => true,
                'transaction_id' => $transactionId,
            ];
        } catch (\Illuminate\Validation\ValidationException $e) {

            $errorMessage = $e->errors()['total'][0] ?? $e->getMessage();
            Log::error('TNG validation error: ' . $errorMessage, ['input' => $request->all()]);
            return [
                'success' => false,
                'error' => 'Invalid total amount: ' . $errorMessage
            ];
        } catch (\Exception $e) {
            Log::error('Unexpected error in TngPaymentStrategy: ' . $e->getMessage(), ['input' => $request->all()]);
            return [
                'success' => false,
                'error' => 'TNG processing failed: ' . $e->getMessage()
            ];
        }
    }
}