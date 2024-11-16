<?php

namespace App\Models;

use DateTime;
use App\Models\MyModels\Main;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Setting extends Main
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['meta_key', 'meta_value'];

    public function getSetting($meta_key)
    {
        $setting = Setting::where('meta_key', $meta_key)->first();
        return $setting?->meta_value ?? "";
    }
    public function mobileCount() {
        return $this->getSetting('mobile-count')?$this->getSetting('mobile-count'):11;
    }
    public function getDaysLeft()
    {
    //    return Cache::rememberForever('daysLeft_' . auth()->user()->account_id, function () {
            $expire = $this->getSetting('charge_expire_date');
            $expire = new DateTime($expire);
            $today = new DateTime();
            return $today->diff($expire)->format('%R%a');
        // });
    }
}
