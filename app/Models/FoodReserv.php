<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FoodReserv extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = ['fp_id', 'p_id'];
}
