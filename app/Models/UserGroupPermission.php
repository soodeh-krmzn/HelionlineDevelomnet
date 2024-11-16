<?php

namespace App\Models;

use App\Models\MyModels\Main;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserGroupPermission extends Main
{
    use HasFactory;

    protected $fillable = ['permission', 'group_id'];
}
