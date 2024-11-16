<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use Spatie\LaravelIgnition\Recorders\DumpRecorder\Dump;
use Symfony\Component\HttpFoundation\Response;

class SetUserTimezone
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            if ($timezone = Session::get('user_timezone')) {
                date_default_timezone_set($timezone);
                return $next($request);
            }
            $setting = new Setting;
            $currentTimezone = $setting->getSetting('timezone');
            if ($currentTimezone == "") {
                $timezone = "Asia/Tehran";
            } else {
                $timezone = $currentTimezone;
            }
            Session::put('user_timezone', $timezone);
            date_default_timezone_set($timezone);
        }
        return $next($request);
    }
}
