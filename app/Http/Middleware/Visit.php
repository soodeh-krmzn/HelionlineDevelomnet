<?php

namespace App\Http\Middleware;

use App\Models\Admin\VisitRecord;
use Closure;
use Illuminate\Http\Request;
use Jenssegers\Agent\Facades\Agent;
use Symfony\Component\HttpFoundation\Response;

class Visit
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        //dump(session()->all());
        $response=$next($request);
        VisitRecord::create([
            'user_id'=>auth()->id(),
            'account_id'=>auth()->user()->account_id,
            'ip'=>getIp(),
            'url'=>request()->url(),
            'route'=>request()->route()->getName(),
            'browser'=>Agent::browser(),
            'device'=>Agent::platform(),
        ]);
        return $response;
    }
}
