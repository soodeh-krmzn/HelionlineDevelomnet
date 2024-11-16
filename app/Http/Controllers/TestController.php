<?php

namespace App\Http\Controllers;

use App\Models\Admin\Menu;
use App\Services\IpPanel;
use Illuminate\Http\Request;
use Illuminate\Support\Benchmark;
use Illuminate\Support\Facades\Cache;

class TestController extends Controller
{
    public function test()
    {
        $message = "ممد عزیز
                    به هلی سافت خیلی خوش اومدی
                    قراره کلی خوش بگذرونی
                    پر انرژی پیش به سوی خوشحالی نامحدود!
                    ساعت ورود 10
                    هلی سافت";
        $result=IpPanel::send('09927836254',$message);
        dd($result);
        // IpPanel::getCredit();
    }
    public function benchmark()
    {
        // abort(403,__('برای ورود به این قسمت لطفا پکیج خودرا افزیش دهید'));
        // cache()->flush();
        Benchmark::dd([
            'ids' => function () {
                $packageMenus = Cache::rememberForever('package_menus1', function () {
                    $package = auth()->user()->account->package;
                    return $package->menus()->where('display_nav', 1)->orderBy('display_order', 'asc')->pluck('menus.id')->toArray();
                });
                $menus = Menu::find($packageMenus);
            },
            'elequent' => function () {
                $packageMenus = Cache::rememberForever('package_menus2', function () {
                    $package = auth()->user()->account->package;
                    return $package->menus()->where('display_nav', 1)->orderBy('display_order', 'asc')->get();
                });
            },
            'usual' => function () {
                $package = auth()->user()->account->package;
                return $package->menus()->where('display_nav', 1)->orderBy('display_order', 'asc')->get();
            }
        ], 20);
    }
}
