<?php

namespace App\Http\Controllers;

use App\Models\Admin\SmsPattern;
use App\Models\Admin\SmsPatternCategory;
use App\Models\Setting;
use Illuminate\Http\Request;
use App\Models\Sync;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SyncController extends Controller
{

    public function getUnsyncedData()
    {
        $unsyncedData = Sync::where('status', 0)->get();
        return response()->json($unsyncedData);
    }

    public function runSql()
    {
        Schema::table('people', function (Blueprint $table) {
            // Check if the column already exists to avoid errors
            if (!Schema::hasColumn('people', 'uuid')) {
                $table->uuid('uuid')->unique()->nullable(false)->default(DB::raw('UUID()'))->after('id');
            }
        });
    }
}
