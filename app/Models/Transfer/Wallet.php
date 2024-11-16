<?php

namespace App\Models\Transfer;

use App\Models\MyModels\Transfer;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wallet extends Transfer
{
    use HasFactory;
    protected $table = 'wallet';
    protected $guarded = [];
}
