<?php

namespace App\Models\Admin;

use App\Models\MyModels\Admin;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LoginRecord extends Admin
{
    use HasFactory;
    protected $table = 'login_records';
    protected $guarded=[];
}
