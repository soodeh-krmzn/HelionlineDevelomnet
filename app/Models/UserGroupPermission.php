<?php

namespace App\Models;

use App\Models\MyModels\Main;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Syncable;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserGroupPermission extends Main
{
    use HasFactory;
    use Syncable;
    use SoftDeletes;

    protected $fillable = ['permission', 'group_id'];
}
