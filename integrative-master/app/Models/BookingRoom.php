<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class BookingRoom extends Pivot
{
    public $timestamps = true;

    protected $fillable = [
        'booking_id',
        'room_id',
        'checkInDate',
        'checkOutDate',
        'pax',
        'status',
    ];

    protected $casts = [
        'checkInDate' => 'datetime',
        'checkOutDate' => 'datetime',
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }
}
