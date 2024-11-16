<?php

namespace App\Models;

use App\Models\MyModels\Main;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SmsLog extends Main
{
    use HasFactory;

    protected $fillable = ['sms_id', 'parts', 'tariff', 'recipient', 'message', 'category_name','sms_panel'];
}
