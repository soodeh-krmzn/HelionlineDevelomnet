<?php

namespace App\Models\Admin;

use App\Models\MyModels\Admin;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdminUser extends Admin
{
    use HasFactory;
    protected $table = 'admins';

    public function getFullName()
    {
        return $this->name . ' ' . $this->family;
    }

}
