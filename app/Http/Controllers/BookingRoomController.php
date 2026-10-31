<?php
// Chin Jia Jun

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BookingRoom;
use App\Models\Room;
use App\Services\RoomHoldService;
use Carbon\Carbon;

class BookingRoomController extends Controller
{
    //Used as api method for AJAX (dynamic change without reload)
    public function fullyBookedDates(Request $request, RoomHoldService $roomHoldService)
    {
        $dates = [];
        $validated = $request->validate([
            'paxNo' => 'required|integer|min:1',
        ]);
        $pax = $validated['paxNo'];
        $today = Carbon::today()->toDateString();

        try {
            // Get all rooms that can accommodate the pax number and are available
            $eligibleRooms = Room::select('id')
                ->whereHas('roomType', function ($query) use ($pax) {
                    $query->where('paxNo', '>=', $pax);
                })
                ->where('availability', true)
                ->get();

            // Get all bookings for these rooms that are still pending and have check-in dates from today onwards
            $bookings = BookingRoom::where('checkInDate', '>', $today)
                ->where('status', 'Pending')
                ->whereIn('room_id', $eligibleRooms)
                ->get();

            // Count bookings per date
            foreach ($bookings as $booking) {
                for ($date = $booking->checkInDate->copy(); $date->lt($booking->checkOutDate); $date->addDay()) {
                    $dateStr = $date->toDateString();

                    if (isset($dates[$dateStr])) {
                        $dates[$dateStr] += 1;
                    } else {
                        $dates[$dateStr] = 1;
                    }
                }
            }

            // Get hold dates from RoomHoldService
            $holdDates = $roomHoldService->getHoldDates($eligibleRooms);
            foreach ($holdDates as $dateStr => $count) {
                if (isset($dates[$dateStr])) {
                    $dates[$dateStr] += $count;
                } else {
                    $dates[$dateStr] = $count;
                }
            }

            // Determine fully booked dates
            $fullyBooked = array_keys(array_filter($dates, function ($count) use ($eligibleRooms) {
                return $count >= $eligibleRooms->count();
            }));

            return response()->json($fullyBooked);

        } catch (\Exception $e) {
            return response()->json(['status' => "error", 'message' => $e->getMessage()], 500);
        }
    }

    // Function to update booking status (Cancelled & Completed)
    public function updateStatus(Request $request, int $id)
    {
        $request->validate([
            'status' => 'required|string|max:20',
        ]);

        try {
            $bookingRoom = BookingRoom::findOrFail($id);
            $bookingRoom->status = $request->status;
            $bookingRoom->save();

            return redirect()->back()->with('success', "Booking {$request->status} Successfully!");
        } catch (\Exception $e) {
            return redirect()->back()->with('error', "Failed to update status: {$request->status}");
        }
    }

    // API endpoint to get all booking rooms (for admin use)
    public function apiGetBookingRooms()
    {
        try {
            $bookingRooms = BookingRoom::all();
            return response()->json([
                'status' => 'success',
                'bookingRooms' => $bookingRooms
            ]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error']);
        }
    }
}
