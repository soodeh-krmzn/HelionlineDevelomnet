<?php

namespace App\Models\Admin;

use App\Models\MyModels\Admin;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Option extends Admin
{
    use HasFactory;

    public static function get_option($meta_key)
    {
        $check = Option::where("meta_key", $meta_key)->first();
        if ($check) {
            return $check->meta_value;
        } else {
            return "";
        }
    }
}
