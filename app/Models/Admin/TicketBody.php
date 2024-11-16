<?php

namespace App\Models\Admin;

use App\Models\User;
use App\Models\MyModels\Admin;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TicketBody extends Admin
{
    use HasFactory;
    protected $table = 'ticket_bodies';
    protected $guarded = [];



    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }

    public function getFile()
    {
        if ($this->file != null) {
            if ($this->account_id == 0) {
                return "https://helisystem.ir/uploads/thickets/" . $this->file;
            } else {
                return "https://helionline.ir/uploads/thickets/" . $this->file;
            }
        } else {
            return false;
        }
    }

    public function user() {
        return $this->belongsTo(User::class);
    }
    public function admin() {
        return $this->belongsTo(AdminUser::class,'user_id');
    }
}
