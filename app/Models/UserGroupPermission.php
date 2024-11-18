<?php

namespace App\Models;

use App\Models\MyModels\Main;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Syncable;

class UserGroupPermission extends Main
{
    use HasFactory;
    use Syncable;

    protected $fillable = ['permission', 'group_id'];
}
