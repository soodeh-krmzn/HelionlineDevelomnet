<?php

namespace App\Http\Middleware;

use App\Models\Setting;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ChargeMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $setting = new Setting;
        if ($setting->getDaysLeft() <= 0) {
            return redirect()->route('charge');
        }
        // if ($setting->getSetting('charge') <= 0) {
        //     return redirect()->route('charge');
        // }
        return $next($request);
    }
}
