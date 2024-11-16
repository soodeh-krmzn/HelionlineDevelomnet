<?php

namespace App\Models\Admin;

use App\Models\Admin\Package;
use App\Models\MyModels\Admin;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Account extends Admin
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name', 'family', 'center', 'phone', 'mobile', 'city', 'town', 'address',
        'days', 'db_name', 'db_user', 'db_pass', 'charge_date', 'status', 'status_detail',
        'sms_charge', 'reserve_charge', 'slug', 'photo', 'zarinpal', 'sms_username', 'sms_password'
    ];
    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    public function package()  {
        return $this->belongsTo(Package::class);
    }

    public function unreadTickets()
    {

        return $this->tickets()->whereHas('chats', function ($q) {
            $q->where('account_id', 0)->where('seen', '0');
        })->count();
    }
}
