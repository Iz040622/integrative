<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $fillable = [
        'bookingDate',
        'user_id',
        'payment_id',
    ];

    protected $casts = [
        'bookingDate' => 'datetime',
    ];

    public function bookingRooms()
    {
        return $this->hasMany(BookingRoom::class);
    }

    public function rooms()
    {
        return $this->belongsToMany(Room::class)
            ->using(BookingRoom::class)
            ->withPivot(['checkInDate', 'checkOutDate', 'pax', 'status'])
            ->withTimestamps();
    }
}
