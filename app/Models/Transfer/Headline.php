<?php

namespace App\Models\Transfer;

use App\Models\MyModels\Transfer;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Headline extends Transfer
{
    use HasFactory;
    protected $table = 'headline';
    protected $guarded = [];
}
