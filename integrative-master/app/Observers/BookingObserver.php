<?php
// Chin Jia Jun

namespace App\Observers;

use App\Models\Booking;
use App\Mail\BookingCreatedMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

class BookingObserver
{
    /**
     * Handle the Booking "created" event.
     */
    public function created(Booking $booking)
    {
        $request = Request::create('/api/customer', 'GET');
        $response = Route::dispatch($request);
        $customer = json_decode($response->getContent(), true);
        // Send email to customer when booking is created
        Mail::to($customer['email'])->send(new BookingCreatedMail($booking));
    }

    /**
     * Handle the Booking "updated" event.
     */
    public function updated(Booking $booking): void
    {
        //
    }

    /**
     * Handle the Booking "deleted" event.
     */
    public function deleted(Booking $booking): void
    {
        //
    }

    /**
     * Handle the Booking "restored" event.
     */
    public function restored(Booking $booking): void
    {
        //
    }

    /**
     * Handle the Booking "force deleted" event.
     */
    public function forceDeleted(Booking $booking): void
    {
        //
    }
}
