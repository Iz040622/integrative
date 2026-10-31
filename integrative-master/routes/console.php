<?php
// Chin Jia Jun

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Models\RoomHold;
use App\Models\BookingRoom;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

//php artisan schedule:run for demo purpose
Schedule::call(function () {
    // Delete expired holds
    RoomHold::where('expires_at', '<', now())->delete();
})->everyMinute();

Schedule::call(function () {
    // Update status of past bookings to 'Completed'
    BookingRoom::where('checkInDate', '<', now()->toDateString())
    ->where('status', 'Pending')
    ->update(['status' => 'Completed']);

})->dailyAt('14:00');


