<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;

class SuperAdminMiddleware
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
        ])->get(url("/api/admin/{$user->id}"));

        if ($response->failed()) {
            abort(403, 'Access denied. Unable to verify admin status.');
        }

        $admin = $response->json();

        // Check if position exists and is superadmin
        if (!isset($admin['position']) || $admin['position'] !== 'Super Admin') {
            abort(403, 'Access denied. Only Super Admins are allowed.');
        }

        return $next($request);
    }
}
