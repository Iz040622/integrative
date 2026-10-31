<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login');
        }

        // Call API to get all admins
        $response = Http::withHeaders([
            'Accept' => 'application/json',
        ])->get(url("/api/admins"));

        if ($response->failed()) {
            abort(403, 'Access denied. Unable to verify admin status.');
        }

        $admins = $response->json();

        // Check if logged-in user is one of the admins
        $isAdmin = false;
        foreach ($admins as $admin) {
            if (isset($admin['user_id']) && $admin['user_id'] == $user->id) {
                $isAdmin = true;
                break;
            }
        }

        if (!$isAdmin) {
            abort(403, 'Access denied. Only Admins are allowed.');
        }

        return $next($request);
    }
}
