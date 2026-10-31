<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;
use App\Services\RoomHoldService;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckCartExpiration
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next)
    {
        if (Session::has('cart') && Session::has('cart_expires_at')) {
            $expiresAt = Session::get('cart_expires_at');

            if (now() >= Carbon::parse($expiresAt)) {
                Session::forget('cart');
                Session::forget('cart_expires_at');
                try {
                    $customer = Http::get(config('app.url') . '/api/customer')->json();
                    $roomHoldService = app(RoomHoldService::class);
                    $roomHoldService->releaseHoldsByUser($customer['id']); 
                } catch (\Exception $e) {

                }
                session()->flash('cart_expired', 'Your cart has expired.');
                return redirect('/cart');
            }
        }

        return $next($request);
    }
}
