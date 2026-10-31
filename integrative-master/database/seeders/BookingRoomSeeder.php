<?php

namespace Database\Seeders;

use App\Models\BookingRoom;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class BookingRoomSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $json = File::get(database_path('data/booking_room.json'));
        $bookingRooms = json_decode($json, true);

        foreach ($bookingRooms as $bookingRoom) {
            BookingRoom::create($bookingRoom);
        }
    }
}
