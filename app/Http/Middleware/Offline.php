<?php

namespace App\Http\Middleware;

use App\Models\Admin\VisitRecord;
use Closure;
use Illuminate\Http\Request;
use Jenssegers\Agent\Facades\Agent;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Admin\License;
class Offline
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        if (License::isOfflineMode()) {
            if ($request->isMethod('post') || $request->isMethod('put') || $request->isMethod('patch') || $request->isMethod('delete')) {
                return response()->json([
                    'message' => 'حالت آفلاین فعال است. امکان ویرایش وجود ندارد.'
                ], 403);
            }
        }

        return $next($request);
    }
}
