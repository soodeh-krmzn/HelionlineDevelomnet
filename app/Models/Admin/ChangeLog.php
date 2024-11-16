<?php

namespace App\Models\Admin;

use App\Models\MyModels\Admin;
use App\Models\Setting;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ChangeLog extends Admin
{
    use HasFactory;
    protected $table = 'change_logs';
    protected $guarded = [];

    public static function boot() {
        parent::boot();
        static::addGlobalScope('lang',function($builder){
            $builder->where('lang',app()->getLocale());
        });
    }
    public function check() {
        $setting= new Setting;
        $lastLogId=$setting->getSetting('last-log-modal-id');
        if ($lastLogId==$this->id) {
           return false;
        }
       $setting->updateOrCreate([
        'meta_key'=>'last-log-modal-id'
       ],[
        'meta_value'=>$this->id
       ]);
       return true;
    }


}
