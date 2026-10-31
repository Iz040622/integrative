<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoomHold extends Model
{
    protected $fillable = [
        'user_id',
        'room_id',
        'checkInDate',
        'checkOutDate',
        'expires_at',
    ];

    protected $casts = [
        'checkInDate' => 'datetime',
        'checkOutDate' => 'datetime',
        'expires_at' => 'datetime',
    ];

    protected static function booted()
    {
        static::creating(function ($hold) {
            if (!$hold->expires_at) {
                $hold->expires_at = now()->addMinutes(15);
            }
        });
    }

    // public function user()
    // {
    //     return $this->belongsTo(User::class);
    // }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }
}
