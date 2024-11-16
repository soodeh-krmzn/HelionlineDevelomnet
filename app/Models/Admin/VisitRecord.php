<?php

namespace App\Models\Admin;

use App\Models\MyModels\Admin;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class VisitRecord extends Admin
{
    use HasFactory;
    protected $table= 'visit_records';
    protected $guarded=[];

}
