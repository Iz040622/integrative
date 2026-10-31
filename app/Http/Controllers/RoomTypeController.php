<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RoomType;
use Carbon\Carbon;

class RoomTypeController extends Controller
{
    public function apiIndex()
    {
        try {
            $roomTypes = RoomType::all();
            return response()->json($roomTypes);
        } catch (\Exception $e) {
            return response()->json([
                'error' => true,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function apiRoomTypesToBook(Request $request)
    {
        $validated = $request->validate([
            'startDate' => 'required|date',
            'endDate' => 'required|date|after:startDate',
            'paxNo' => 'required|integer|min:1',
        ]);

        $startDate = Carbon::parse($validated['startDate']);
        $endDate = Carbon::parse($validated['endDate']);
        $pax = $validated['paxNo'];

        $availableRoomTypes = RoomType::where('paxNo', '>=', $pax)
            ->whereHas('rooms', function ($query) use ($startDate, $endDate) {
                $query->where('availability', true)
                    ->whereDoesntHave('bookingRooms', function ($query2) use ($startDate, $endDate) {
                        $query2->where('status', 'Pending')
                            ->where('checkInDate', '<', $endDate)
                            ->where('checkOutDate', '>', $startDate);
                    })
                    ->whereDoesntHave('roomHolds', function ($query3) use ($startDate, $endDate) {
                        $query3->where('expires_at', '>', now()) 
                            ->where('checkInDate', '<', $endDate)
                            ->where('checkOutDate', '>', $startDate);
                    });
            })
            ->get();

        return response()->json($availableRoomTypes);
    }
}
