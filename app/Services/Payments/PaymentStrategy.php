<?php
// LEOW SHI HAO 2314003 G3
namespace App\Services\Payments;

use Illuminate\Http\Request;

interface PaymentStrategy
{
    public function processPayment(Request $request): array;
}