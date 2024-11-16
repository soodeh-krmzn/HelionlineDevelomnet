<?php

namespace App\Services;

use App\Models\Setting;
use IPPanel\Errors\Error;

class IpPanel
{
    public static function send($mobile, $message) {
            $setting=new Setting();
            $apiKey = $setting->getSetting('sms_api_key');
            $sendNumber=$setting->getSetting('sms_number');
            $client = new \IPPanel\Client($apiKey);
            // dd($client->getCredit());
            try {
                $messageId = $client->send(
                    $sendNumber,          // originator
                    [$mobile],    // recipients
                    $message,// message
                    "description"        // is logged
                );
                return $messageId ;
            } catch (Error $e) {
                // return ['error'=>'شماره خط وارد شده صحیح نمی باشد'];
                return ['error'=>$e->unwrap()];
             }catch(HttpException $e){
                return ['error'=>$e->getMessage()];
             }
    }

    public static function checkConection() {
        $setting=new Setting();
        $account=auth()->user()->account;
        $apiKey = $setting->getSetting('sms_api_key');
        $client = new \IPPanel\Client($apiKey);
        try {
             $credit=cnf($client->getCredit());
             try {
                $message="مدیر گرامی تست اتصال پنل پیامکی شما با موفقیت انجام شد.\nبا تشکر هلی سافت";
                self::send($account->mobile,$message);
                return $credit;
             } catch (Error $e) {
                // return ['error'=>'شماره خط وارد شده صحیح نمی باشد'];
                return ['error'=>$e->unwrap()];
             }catch(HttpException $e){
                return ['error'=>$e->getMessage()];
             }
        } catch (\Throwable $th) {
            return ['error'=>'کلید وارد شده صحیح نمی باشد'];
        }
    }
}
