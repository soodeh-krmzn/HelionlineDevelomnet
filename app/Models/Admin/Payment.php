<?php

namespace App\Models\Admin;

use App\Models\MyModels\Admin;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Payment extends Admin
{
    use HasFactory;

    public function getStatus()
    {
        if ($this->status == "OK") {
            return '<span class="badge bg-success">موفق</span>';
        } else if ($this->status == "NOK") {
            return '<span class="badge bg-danger">ناموفق</span>';
        } else {
            return '<span class="badge bg-warning">نامشخص</span>';
        }
    }
    public function getStatusForExcel()
    {
        if ($this->status == "OK") {
            return __('موفق');
        } else if ($this->status == "NOK") {
            return __('ناموفق');
        } else {
            return __('نامشخص');
        }
    }
    public function getTypeValue()
    {

        switch ($this->type) {
            case 'sms':
                return __('پیامک');
                break;
            case 'account':
                return __('اشتراک');
                break;
            default:
                return $this->type;
                break;
        }
    }
}
