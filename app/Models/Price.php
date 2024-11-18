<?php

namespace App\Models;

use App\Models\MyModels\Main;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Gate;
use App\Traits\Syncable;

class Price extends Main
{
    use HasFactory;
    use Syncable;

    protected $fillable = ['section_id','entrance_price', 'from', 'to', 'calc_type', 'price', 'price_type'];

    public function priceTable($sectionId)
    {
        return view('section.parts.prices-form',compact('sectionId'));
    }

}
