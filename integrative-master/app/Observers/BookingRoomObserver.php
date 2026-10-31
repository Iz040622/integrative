<?php
// Chin Jia Jun

namespace App\Observers;

use App\Models\BookingRoom;
use App\Mail\BookingRoomCancelledMail;
use App\Mail\BookingRoomCompletedMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

class BookingRoomObserver
{
    /**
     * Handle the BookingRoom "created" event.
     */
    public function created(BookingRoom $bookingRoom): void
    {
        //
    }

    /**
     * Handle the BookingRoom "updated" event.
     */
    public function updated(BookingRoom $bookingRoom): void
    {
        $request = Request::create('/api/customer', 'GET');
        $response = Route::dispatch($request);
        $customer = json_decode($response->getContent(), true);

        // Send email to customer when booking room is cancelled or completed
        if ($bookingRoom->isDirty('status') && $bookingRoom->status === 'Cancelled') {
            Mail::to($customer['email'])->send(new BookingRoomCancelledMail($bookingRoom));
        } else if ($bookingRoom->isDirty('status') && $bookingRoom->status === 'Completed') {
            Mail::to($customer['email'])->send(new BookingRoomCompletedMail($bookingRoom));
        }
    }

    /**
     * Handle the BookingRoom "deleted" event.
     */
    public function deleted(BookingRoom $bookingRoom): void
    {
        //
    }

    /**
     * Handle the BookingRoom "restored" event.
     */
    public function restored(BookingRoom $bookingRoom): void
    {
        //
    }

    /**
     * Handle the BookingRoom "force deleted" event.
     */
    public function forceDeleted(BookingRoom $bookingRoom): void
    {
        //
    }
}
