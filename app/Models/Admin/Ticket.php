<?php

namespace App\Models\Admin;

use App\Models\MyModels\Admin;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Admin
{
    use HasFactory;
    protected $table = 'tickets';
    protected $guarded = [];
    public static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->lang = app()->getLocale();
        });
    }

    public function chats()
    {
        return $this->hasMany(TicketBody::class);
    }

    public function lastMsgTime()
    {
        return $this->chats()->latest('created_at')->value('created_at')->diffForHumans(['parts'=>1]);
    }
}
