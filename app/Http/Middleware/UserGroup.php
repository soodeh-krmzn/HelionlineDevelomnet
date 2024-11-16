<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Admin\Menu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class UserGroup
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $url = getUrl();
        $menu = Menu::where('url', $url)->firstOrFail();
        // $packageMenus = Cache::rememberForever('package_menus', function () {
        $package = auth()->user()->account->package;
        $packageMenus = $package->menus()->where('display_nav', 1)->orderBy('display_order', 'asc')->pluck('menus.id')->toArray();
        // });
        if (!in_array($menu->id, $packageMenus)) {
            abort(403, __('برای ورود به این قسمت لطفا پکیج خودرا افزیش دهید'));
        }
        // if (auth()->user()->access != 1) {
        //     if (!auth()->user()->group?->menus->contains($menu->id)) {
        //         abort(403);
        //     }
        // }
        return $next($request);
    }
}
