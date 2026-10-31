<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoomType extends Model
{
    protected $table = 'room_types';
    protected $fillable = [
        'name',
        'description',
        'roomAmenities',
        'paxNo',
        'price',
        'imageUrl',
        'availability'
    ];

    protected $casts = [
        'roomAmenities' => 'array',
    ];

    public function rooms()
    {
        return $this->hasMany(Room::class);
    }
}
