<?php

namespace App\Models;

use App\Models\MyModels\Admin;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class QComponent extends Admin
{
    use HasFactory,SoftDeletes;
    protected $table='question_components';

    public function questions() {
        return $this->hasMany(CommonQuestion::class,'component_id');
    }
}
