<?php

namespace App\Http\Controllers;

use App\Models\PromoCode;

class PromoCodeController extends Controller
{
    public function apiGetPromo($code)
    {
        $promo = PromoCode::where('promoteCode', $code)->first();

        if (!$promo) {
            return response()->json([
                'success' => false,
                'message' => 'Promo code not found'
            ], 404);
        }

        // Check date validity
        $today = now()->toDateString();
        if ($promo->startDate > $today || $promo->endDate < $today) {
            return response()->json([
                'success' => false,
                'message' => 'Promo code expired or not yet active'
            ], 400);
        }

        return response()->json([
            'success' => true,
            'promo' => [
                'id' => $promo->id,
                'code' => $promo->promoteCode,
                'description' => $promo->description,
                'discount' => $promo->discount,
                'startDate' => $promo->startDate,
                'endDate' => $promo->endDate
            ]
        ]);
    }
}
