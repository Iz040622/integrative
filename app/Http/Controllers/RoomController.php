<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Room;
use Carbon\Carbon;

class RoomController extends Controller
{
    public function apiGetAvailableRoomByRoomType(Request $request)
    {
        $validated = $request->validate([
            'roomTypeId' => 'required|integer|exists:room_types,id',
            'startDate' => 'required|date',
            'endDate' => 'required|date|after:startDate',
        ]);

        $roomTypeId = $validated['roomTypeId'];
        $startDate = Carbon::parse($validated['startDate']);
        $endDate = Carbon::parse($validated['endDate']);

        try {
            $room = Room::where('room_type_id', $roomTypeId)
                ->where('availability', true)
                ->whereDoesntHave('bookingRooms', function ($query) use ($startDate, $endDate) {
                    $query->where('status', 'Pending')
                        ->where('checkInDate', '<', $endDate)
                        ->where('checkOutDate', '>', $startDate);
                })
                ->whereDoesntHave('roomHolds', function ($query3) use ($startDate, $endDate) {
                        $query3->where('expires_at', '>', now()) 
                            ->where('checkInDate', '<', $endDate)
                            ->where('checkOutDate', '>', $startDate);
                    })
                ->first();

            return response()->json([
                'status' => 'success',
                'room' => $room
            ]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error']);
        }
    }

    public function apiGetRoomType($roomId)
    {
        try {
            $room = Room::with('roomType')->findOrFail($roomId);
            return response()->json($room->roomType);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Room not found'], 404);
        }
    }

    public function apiGetRoom($roomId)
    {
        try {
            $room = Room::findOrFail($roomId);
            return response()->json($room);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Room not found'], 404);
        }
    }
}
