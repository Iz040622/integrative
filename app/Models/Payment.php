<?php
// LEOW SHI HAO 2314003 G3
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute; 

class Payment extends Model
{
    use HasFactory;

    protected $table = 'payment';

    protected $fillable = [
        'paymentDate',
        'paymentTime',
        'paymentMethod',
        'subTotal',
        'totalAmount',
        'invoiceUrl',
        'promo_id',
    ];

    protected $casts = [
        'promo_id'    => 'integer',
        'subTotal'    => 'encrypted:double', 
        'totalAmount' => 'encrypted:double', 
        'invoiceUrl'  => 'encrypted',        
    ];

    public function booking()
    {
        return $this->hasOne(Booking::class, 'payment_id');
    }

    public function promoCode()
    {
        return $this->belongsTo(PromoCode::class, 'promo_id');
    }
}