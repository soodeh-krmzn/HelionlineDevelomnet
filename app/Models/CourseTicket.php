<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CourseTicket extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = ['c_id', 'num', 'name', 'price', 'date', 'disprice', 'presence'];
}
