<?php

namespace App\Services;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;

class Bale
{
    public $token = "31422563:hJvvNVwHFvknVSZoJJSCJLuSVSNZJaQajx9HWUEr";

    public function send($text)
    {
        $url = "https://tapi.bale.ai/bot" . $this->token . "/sendMessage";
        $chat_id = "2053240814";
        $name = Auth::user()->name . ' ' . Auth::user()->family;
        $center_name = (new \App\Models\Setting())->getSetting('center_name');
        $res = Http::get($url, [
            'chat_id' => $chat_id,
            'text' => 'از: ' . $name . ' (' . $center_name . '): ' . $text
        ]);
    }
}
