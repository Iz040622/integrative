<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Booking;
use Illuminate\Support\Facades\File;

class BookingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $json = File::get(database_path('data/booking.json'));
        $bookings = json_decode($json, true);

        foreach ($bookings as $booking) {
            Booking::create($booking);
        }
    }
}
