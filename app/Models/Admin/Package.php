<?php

namespace App\Models\Admin;

use App\Models\MyModels\Admin;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Package extends Admin
{
    use HasFactory;

    public function menus() {
        return $this->belongsToMany(Menu::class,'package_menu');
    }

}
