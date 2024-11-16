<?php

namespace App\Models\Admin;

use App\Models\MyModels\Admin;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class JobReport extends Admin
{
    use HasFactory;

    protected $fillable = ['account_id', 'class_name', 'status', 'error', 'details'];
}
