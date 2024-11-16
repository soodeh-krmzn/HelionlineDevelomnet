<?php

namespace App\Models;

use App\Models\MyModels\Main;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MyPayment extends Main
{
    use HasFactory;

    protected $fillable = ['authority', 'status', 'ref_id', 'message', 'price', 'type', 'username', 'card', 'pay_created_at'];

    public function getStatus()
    {
        if($this->status == "OK") {
            return '<span class="badge bg-success">موفق</span>';
        } else if($this->status == "NOK") {
            return '<span class="badge bg-danger">ناموفق</span>';
        } else {
            return '<span class="badge bg-warning">نامشخص</span>';
        }
    }
}
