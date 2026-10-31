<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    protected $table = 'rooms';

    protected $fillable = [
        'roomNo',
        'availability',
        'room_type_id',
    ];

    public function roomType()
    {
        return $this->belongsTo(RoomType::class);
    }

    public function roomHolds()
    {
        return $this->hasMany(RoomHold::class);
    }

    public function bookingRooms()
    {
        return $this->hasMany(BookingRoom::class);
    }

    public function bookings()
    {
        return $this->belongsToMany(Booking::class)
            ->using(BookingRoom::class)
            ->withPivot(['checkInDate', 'checkOutDate', 'pax', 'status'])
            ->withTimestamps();
    }
}
