<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BuyPackage extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = ['p_id', 'pk_id', 'time', 'expire', 'type'];
}
