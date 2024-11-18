<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Syncable;

class FoodPlan extends Model
{
    use HasFactory;
    use SoftDeletes;
    use Syncable;

    protected $fillable = ['date', 'f_id'];
}
