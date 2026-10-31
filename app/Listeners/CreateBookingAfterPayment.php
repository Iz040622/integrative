<?php
// Chin Jia Jun

namespace App\Listeners;

use App\Services\RoomHoldService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Events\PaymentSuccessful;
use App\Models\Booking;
use App\Models\BookingRoom;
use Illuminate\Support\Facades\DB;

class CreateBookingAfterPayment
{
    /**
     * Create the event listener.
     */
    protected $roomHoldService;

    public function __construct(RoomHoldService $roomHoldService)
    {
        $this->roomHoldService = $roomHoldService;
    }

    /**
     * Handle the event.
     */
    public function handle(PaymentSuccessful $event): void
    {
        try {
            DB::transaction(function () use ($event) {
                // Create booking record
                $booking = Booking::create([
                    'bookingDate' => now()->toDateString(),
                    'user_id' => $event->userId,
                    'payment_id' => $event->paymentId,
                ]);

                // Create booking room records
                $data = array_map(function ($item) use ($booking) {
                    return [
                        'booking_id' => $booking->id,
                        'room_id' => $item['room_id'],
                        'checkInDate' => $item['startDate'],
                        'checkOutDate' => $item['endDate'],
                        'pax' => $item['paxNo'],
                        'status' => 'Pending',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }, session('cart'));

                BookingRoom::insert($data);

                // Clear cart and release holds
                session()->forget('cart');
                session()->forget('cart_expires_at');
                $this->roomHoldService->releaseHoldsByUser($event->userId);
            });
        } catch (\Exception $e) {
        }
    }
}
