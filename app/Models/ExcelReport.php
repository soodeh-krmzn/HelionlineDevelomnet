<?php

namespace App\Models;

use App\Models\MyModels\Main;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ExcelReport extends Main
{
    use HasFactory;

    protected $fillable = ['user_id', 'table', 'name', 'link'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
