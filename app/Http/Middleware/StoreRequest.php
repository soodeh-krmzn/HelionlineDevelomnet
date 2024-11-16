<?php

namespace App\Http\Middleware;

use App\Models\Request as ModelsRequest;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class StoreRequest
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        ModelsRequest::create([
            'user_id' => auth()->id(),
            'ip' => getIp(),
            'url' => request()->route()->uri,
            'method' => strtoupper(request()->getMethod())
        ]);

        return $response;
    }
}
