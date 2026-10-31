<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PromoCode extends Model
{
    protected $table = 'promo_code';

    protected $fillable = [
        'startDate',
        'endDate',
        'discount',
        'description',
        'promoteCode',
    ];

    protected $dates = [
        'startDate',
        'endDate',
    ];
}
