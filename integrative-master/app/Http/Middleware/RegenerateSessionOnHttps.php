<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RegenerateSessionOnHttps
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        if ($request->secure() && !session()->has('https_regenerated')) {
            session()->regenerate(); 
            session(['https_regenerated' => true]); 
        }

        return $next($request);
    }
}
