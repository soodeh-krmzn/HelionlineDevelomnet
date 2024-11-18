<?php

namespace App\Http\Controllers;

use App\Models\Admin\SmsPattern;
use App\Models\Admin\SmsPatternCategory;
use App\Models\Setting;
use Illuminate\Http\Request;
use App\Models\Sync;

class SyncController extends Controller
{

    public function getUnsyncedData()
    {
        $unsyncedData = Sync::where('status', 0)->get();
        return response()->json($unsyncedData);
    }

}
