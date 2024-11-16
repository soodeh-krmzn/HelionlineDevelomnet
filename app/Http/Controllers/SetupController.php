<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Services\Export;
use App\Exports\ExcelExport;
use Illuminate\Http\Request;
use App\Models\Admin\ChangeLog;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class SetupController extends Controller
{
    public function index()
    {
        return view('setup.index');
    }
    public function notification()
    {
        $items=ChangeLog::latest()->get();
        return view('setup.notifications',compact('items'));
    }

    public function store(Request $request)
    {
        $action = $request->action;
        if ($action == "done") {
            Setting::updateOrCreate([
                'meta_key' => "setup"
            ], [
                'meta_value' => "done"
            ]);
        } else {
            return response()->json([
                'message' => 'خطایی در هنگام راه اندازی رخ داده است. لطفا با پشتیبان در ارتباط باشید.'
            ], 404);
        }
    }

}
