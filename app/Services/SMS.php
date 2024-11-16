<?php

namespace App\Services;

use App\Models\Admin\Option;
use App\Models\Setting;
use App\Models\SmsLog;
use App\Models\Admin\SmsPattern;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use SoapClient;

class SMS
{

    public $username = "arsha-74390";
    public $password = "XuKAZDHX0i2USZKA";
    public $domain = "magfa";

    public function send_sms($category, $mobile, array $params)
    {
        $setting = new Setting();
        $id = $setting->getSetting($category) ?? 0;
        $smsPattern = SmsPattern::find($id);
        if ($smsPattern) {
            $message = str_replace("[center]", $setting->getSetting('center_name'), $smsPattern->text);
            $message = str_replace("[sign]", $setting->getSetting('sms_sign'), $message);
            foreach ($params as $key => $value) {
                $message = str_replace("[" . $key . "]", $value, $message);
            }

            if (!$this->check_charge()) {
                return;
            }
            if ($sms_panel=$setting->getSetting('sms_panel')) {
                $response = IpPanel::send($mobile, $message);
                if ($response['error']??false) {
                    $message=$response['error'];
                    $response_id='-';
                }else{
                    $response_id=$response;
                }
                $log = SmsLog::create([
                    'sms_id' => $response_id,
                    'parts' => null,
                    'tariff' => null,
                    'paenl' => $sms_panel,
                    'recipient' => $mobile,
                    'message' => $message,
                    'category_name' => $category
                ]);
            } else {
                $response = $this->send($mobile, $message);
                $log = SmsLog::create([
                    'sms_id' => $response->messages->id ?? null,
                    'parts' => $response->messages->parts ?? null,
                    'tariff' => $response->messages->tariff ?? null,
                    'recipient' => $mobile,
                    'paenl' => "هلی سافت",
                    'message' => $message,
                    'category_name' => $category
                ]);

                $this->charge($log->parts);
            }
        }
    }

    public function check_charge(): bool
    {
        $charge = (new Setting())->getSetting('sms_charge') ?? 0;
        $min_charge = (new Option())->get_option('min_sms_charge') ?? 0;
        return $charge > $min_charge;
    }

    public function charge($parts)
    {
        $setting = Setting::where('meta_key', 'sms_charge')->first();
        $setting->meta_value -= $parts;
        $setting->save();
        auth()->user()->account->update([
            'sms_charge' => $setting->meta_value
        ]);
    }

    public function send($mobile, $message)
    {
        $url = "https://sms.magfa.com/api/soap/sms/v2/server?wsdl";

        $options = [
            'login' => "$this->username/$this->domain",
            'password' => $this->password, // -Credientials
            'cache_wsdl' => WSDL_CACHE_NONE, // -No WSDL Cache
            'compression' => (SOAP_COMPRESSION_ACCEPT | SOAP_COMPRESSION_GZIP | 5), // -Compression *
            'trace' => false, // -Optional (debug)
            'stream_context' => stream_context_create(
                [
                    'ssl' => [
                        'verify_peer' => false,
                        'verify_peer_name' => false,
                        'allow_self_signed' => true
                    ]
                ]
            )
        ];

        $client = new SoapClient($url, $options);

        // send
        $response = $client->send(
            [$message], // messages
            ["+98300074390"], // short numbers can be 1 or same count as recipients (mobiles)
            [$mobile], // recipients
            [], // client-side unique IDs.
            [], // Encodings are optional, The system will guess it, itself ;)
            [], // UDHs, Please read Magfa UDH Documnet
            [] // Message priorities (unused).
        );
        return $response;

        //$status = $response->status;
        //$parts = $response->parts;
        //$tariff = $response->tariff;
        // $id = $response->id;
    }

    // function smsLog($message = null, $senderNumber = null, $receiverNumber, $sendDateTime, $res_data = null, $getDeliver, $model, $details = null, $user_id = null)
    // {
    //     if (Auth::check()) {
    //         $user_id = Auth::user()->id;

    //         $smsData = SmsLog::create([
    //             'user_id' => $user_id,
    //             'message' => $message,
    //             'sender_number' => $senderNumber,
    //             'receiver_number' => $receiverNumber,
    //             'send_datetime' => $sendDateTime,
    //             'status' => $getDeliver,
    //             'res_data' => $res_data,
    //             'details' => $details,
    //             'model' => $model
    //         ]);

    //         return $smsData;
    //     } else {
    //         $smsData = SmsLog::create([
    //             'user_id' => $user_id,
    //             'message' => $message,
    //             'sender_number' => $senderNumber,
    //             'receiver_number' => $receiverNumber,
    //             'send_datetime' => $sendDateTime,
    //             'status' => $getDeliver,
    //             'res_data' => $res_data,
    //             'details' => $details,
    //             'model' => $model
    //         ]);

    //         return $smsData;
    //     }
    // }

}
