<?php

namespace App\Models\Admin;

use App\Models\Admin\Package;
use App\Models\MyModels\Admin;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class License extends Admin
{
    use HasFactory, SoftDeletes;

    public function licenseStatus()
    {
        $licesne = License::where('account_id', auth()->user()->account_id)->first();
        if ($licesne) {
            if ($licesne->status == 1) {
                return true;
            } else {
                return false;
            }
        }
    }

    public function licenseActivate()
    {
        $licesne = License::where('account_id', auth()->user()->account_id)->first();
        if ($licesne) {
            if ($licesne->isActive == 1) {
                return true;
            } else {
                return false;
            }
        }
    }

    public function licenseActivateUser()
    {
        $licesne = License::where('account_id', auth()->user()->account_id)->first();
        if ($licesne) {
            if ($licesne->isActive == 1 && $licesne->userActive != null) {
                return User::where('id', $licesne->userActive)->first();
            } else {
                return false;
            }
        }
    }

    // public function licenseChangeActivate()
    // {
    //     $licesne = License::where('account_id', auth()->user()->account_id)->first();
    //     if ($licesne) {
    //         if ($licesne->isActive == 1) {
    //             $licesne->isActive = 0;
    //             $licesne->userActive = null;
    //         } else {
    //             return false;
    //         }
    //     }
    // }
}
