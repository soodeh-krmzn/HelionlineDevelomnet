<?php

namespace App\Models;

use App\Models\MyModels\Admin;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class CommonQuestion extends Admin
{
    use HasFactory,SoftDeletes;
    protected $table = 'questions';

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope('lang', function ($builder) {
            $builder->where('lang', app()->getLocale());
        });
    }
    public function component() {
        return $this->belongsTo(QComponent::class, 'component_id');
    }
    public static function presentCoponents() {
     $components= CommonQuestion::select('component_id')->distinct()->pluck('component_id');
     return QComponent::find($components);
    }
}
