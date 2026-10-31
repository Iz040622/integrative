<?php
// Chin Jia Jun

namespace App\Services;

use App\Models\RoomHold;
use Exception;
use Carbon\Carbon;

class RoomHoldService
{
    // Hold a room for a user with a 15-minute expiration
    public function holdRoom(int $userId, int $roomId, string $checkInDate, string $checkOutDate)
    {
        try {
            RoomHold::where('user_id', $userId)
                ->where('expires_at', '>', now())
                ->update(['expires_at' => now()->addMinutes(15)]);

            $created = RoomHold::create([
                'user_id' => $userId,
                'room_id' => $roomId,
                'checkInDate' => $checkInDate,
                'checkOutDate' => $checkOutDate,
            ]);

            return $created;
        } catch (Exception $e) {
            return back()->withErrors(['cartError' => 'Failed to release the room hold. Please contact support.'])->withInput();
        }
    }

    // Get hold dates for given room IDs
    public function getHoldDates($room_ids)
    {
        try {
            $holds = RoomHold::whereIn('room_id', $room_ids)
                ->where('expires_at', '>', now())
                ->where('checkInDate', '>', Carbon::today()->toDateString())
                ->get();

            $dates = [];
            foreach ($holds as $hold) {
                for ($date = $hold->checkInDate->copy(); $date->lt($hold->checkOutDate); $date->addDay()) {
                    $dateStr = $date->toDateString();

                    if (isset($dates[$dateStr])) {
                        $dates[$dateStr] += 1;
                    } else {
                        $dates[$dateStr] = 1;
                    }
                }
            }

            return $dates;
        } catch (Exception $e) {
            return response()->json(['status' => "error", 'message' => $e->getMessage()], 500);
        }
    }

    // Release hold for a specific room and refresh other holds for the user
    public function releaseHoldByRoomId(int $userId, int $roomId)
    {
        try {
            RoomHold::where('room_id', $roomId)
                ->where('user_id', $userId)
                ->delete();

            RoomHold::where('user_id', $userId)
                ->where('expires_at', '>', now())
                ->update(['expires_at' => now()->addMinutes(15)]);
        } catch (Exception $e) {
            return back()->withErrors(['cartError' => 'Failed to release the room hold. Please contact support.'])->withInput();
        }
    }

    // Release all holds for a specific user
    public function releaseHoldsByUser(int $userId)
    {
        try {
            RoomHold::where('user_id', $userId)
                ->delete();
        } catch (Exception $e) {
            
        }
    }
}
