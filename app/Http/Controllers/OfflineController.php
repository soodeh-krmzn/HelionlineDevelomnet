<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

class OfflineController extends Controller
{
    public function toggle(Request $request)
    {
        $offlineMode = $request->input('offline_mode') ? 1 : 0;

        Setting::updateOrCreate(
            ['meta_key' => 'offline_mode'],
            ['meta_value' => $offlineMode]
        );

        return response()->json([
            'message' => $offlineMode ? 'حالت آفلاین فعال شد.' : 'حالت آفلاین غیرفعال شد.',
        ]);
    }
}
