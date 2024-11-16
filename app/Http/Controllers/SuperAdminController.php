<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use function Laravel\Prompts\alert;

use Illuminate\Support\Facades\File;
use RealRashid\SweetAlert\Facades\Alert;

class SuperAdminController extends Controller
{
    public function translate()  {
       // $langValue=app()->getLocale();
        switch (app()->getLocale()) {
            case 'en':
                $langValue='انگلیسی';
                $dirClass='dir-ltr';
                break;
            case 'fa':
                return to_route('dashboard');
                break;
        }

        $data=json_decode(File::get(base_path('lang/en.json')),true);
        return view('superadmin.translate',compact('data','langValue','dirClass'));
    }

    public function push(Request $request) {
        $data=array_combine($request->keys,$request->values);
        $data = array_filter($data, function($key) {
            return strlen($key) > 0;
        }, ARRAY_FILTER_USE_KEY);
       File::put(base_path('lang/en.json'),json_encode($data));
       Alert::success('موفق',"فایل ترجمه بروز شد");
       return back();
    }

}
