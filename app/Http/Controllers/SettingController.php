<?php

namespace App\Http\Controllers;

use App\Models\Offer;
use App\Models\Section;
use App\Models\Setting;
use App\Models\PaymentType;
use Illuminate\Http\Request;
use App\Models\Admin\SmsPattern;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Session;
use App\Models\Admin\SmsPatternCategory;
use App\Services\IpPanel;

class SettingController extends Controller
{

    public function global()
    {
        $setting = new Setting;
        $paymentType = new PaymentType;
        return view('setting.global', compact('setting', 'paymentType'));
    }

    public function webServices(Request $request) {
        $setting =new Setting;
        if ($request->has('sms_api_key')) {
            // dd($request->all());
            $setting->updateorCreate([
                'meta_key'=>'sms_api_key'
            ],[
                'meta_value'=>$request->sms_api_key?$request->sms_api_key:''
            ]);
            $setting->updateorCreate([
                'meta_key'=>'sms_number'
            ],[
                'meta_value'=>$request->sms_number?$request->sms_number:''
            ]);
            $setting->updateorCreate([
                'meta_key'=>'sms_panel'
            ],[
                'meta_value'=>$request->sms_panel?$request->sms_panel:''
            ]);
            return ;
        }else if($request->target=='check_sms_panel_status'){
           $result= IpPanel::checkConection();
           if($result['error']??false){
            return response()->json(['error'=>$result['error']??''],500);
           }
           return $result;
        }
      return view('setting.webServices',compact('setting'));
    }

    public function printSetting(Request $request)
    {
        $setting = new Setting;
        if ($request->has('titleSize')) {
            $setting->updateorCreate([
                'meta_key'=>'titleSize'
            ],[
                'meta_value'=>$request->titleSize?$request->titleSize:''
            ]);
            $setting->updateorCreate([
                'meta_key'=>'textSize'
            ],[
                'meta_value'=>$request->textSize?$request->textSize:''
            ]);
            $setting->updateorCreate([
                'meta_key'=>'headSize'
            ],[
                'meta_value'=>$request->headSize?$request->headSize:''
            ]);
            $setting->updateorCreate([
                'meta_key'=>'bill_sign'
            ],[
                'meta_value'=>$request->bill_sign?$request->bill_sign:''
            ]);
            $setting->updateorCreate([
                'meta_key'=>'bill_header'
            ],[
                'meta_value'=>$request->bill_header?$request->bill_header:''
            ]);
            $setting->updateorCreate([
                'meta_key'=>'currSize'
            ],[
                'meta_value'=>$request->currSize?$request->currSize:''
            ]);
        }
        return view('setting.printSetting', compact('setting'));
    }

    public function sms()
    {
        $setting = new Setting;
        $SmsPatternCategory = new SmsPatternCategory;
        $SmsPattern = new SmsPattern;
        return view('setting.sms', compact('setting', 'SmsPatternCategory', 'SmsPattern'));
    }

    public function personalize()
    {
        // dd('dd');
        $setting = new Setting;
        $SmsPatternCategory = new SmsPatternCategory;
        $SmsPattern = new SmsPattern;
        return view('setting.personalize', compact('setting'));
    }

    public function price(Request $request)
    {
        if ($request->has('roundStatus')) {
            // dd($request->all());
            Setting::updateOrCreate([
                'meta_key' => 'round-status'
            ], [
                'meta_value' => $request->roundStatus
            ]);

            Setting::updateOrCreate([
                'meta_key' => 'round-type'
            ], [
                'meta_value' => $request->roundType
            ]);

            Setting::updateOrCreate([
                'meta_key' => 'round-odd'
            ], [
                'meta_value' => $request->roundOdd
            ]);

            Setting::updateOrCreate([
                'meta_key' => 'vat'
            ], [
                'meta_value' => $request->vat ? $request->vat : ''
            ]);

            Setting::updateOrCreate([
                'meta_key' => 'curr'
            ], [
                'meta_value' => $request->curr
            ]);
        }
        $setting = new Setting;
        $section = new Section;
        $offers = Offer::all();
        return view('setting.price', compact('setting', 'section', 'offers'));
    }



    public function club()
    {
        $setting = new Setting;
        return view('setting.club', compact('setting'));
    }

    public function wallet()
    {
        $setting = new Setting;
        return view('setting.wallet', compact('setting'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'image' => 'nullable|max:1024|image|mimes:png,jpg'
        ]);
        parse_str($request->list, $data);
        // dd(stripos($request->list,'timezo3ne'),$request->list);
        if (stripos($request->list, 'timezone') !== false) {
            Session::forget('user_timezone');
        }
        foreach ($data as $key => $value) {
            Setting::updateOrCreate([
                'meta_key' => $key
            ], [
                'meta_value' => $value
            ]);
        }

        $this->handleImage($request);
    }

    protected function handleImage(Request $request)
    {
        $setting = new Setting;
        $avatar = $setting->getSetting('avatar');

        $image_url = '';
        if ($request->image != '') {
            $image_name = now()->timestamp . '_' . $request->image->getClientOriginalName();
            $image_path = public_path('uploads/avatar');
            $request->image->move($image_path, $image_name);

            $image_url = 'uploads/avatar/' . $image_name;
        }

        if ($image_url != '') {

            if (File::exists($avatar)) {
                File::delete($avatar);
            }

            Setting::updateOrCreate([
                'meta_key' => 'avatar'
            ], [
                'meta_value' => $image_url
            ]);
        }

        if ($request->no_image == "true") {
            if (File::exists($avatar)) {
                File::delete($avatar);
            }

            Setting::updateOrCreate([
                'meta_key' => 'avatar'
            ], [
                'meta_value' => ''
            ]);
        }
    }
}
