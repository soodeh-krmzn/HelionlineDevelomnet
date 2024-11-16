<?php

namespace App\Http\Controllers;

use App\Models\SmsLog;
use App\Services\Export;
use Hekmatinasser\Verta\Facades\Verta;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class SmsLogController extends Controller
{
    public function index()
    {
        return view('sms-log.index');
    }

    public function search(Request $request)
    {
        $mobile = $request->mobile;
        $category = $request->category;

        if (!is_null($request->from_date)) {
            $from_date = dateSetFormat($request->from_date);
        } else {
            $from_date = null;
        }
        if (!is_null($request->to_date)) {
            $to_date = dateSetFormat($request->to_date,1);
        } else {
            $to_date = null;
        }

        $logs = SmsLog::query();

        if ($mobile != null) {
            $logs->where('recipient', 'like', '%' . $mobile . '%');
        }
        if ($category != null) {
            $logs->where('category_name', $category);
        }
        if ($from_date != null) {
            $logs->where('created_at', '>=', $from_date);
        }
        if ($to_date != null) {
            $logs->where('created_at', '<=', $to_date);
        }

        $logs = $logs->latest();
        return $logs;
    }

    public function export(Request $request)
    {
        $data = $this->search($request)->get();
        return Export::export($data, [
            'recipient', 'message', 'category_name'
        ]);
    }

    public function dataTable(Request $request)
    {
        if ($request->ajax()) {
            if ($request->has('filter_search')) {
                $data = $this->search($request);
            } elseif ($request->all) {
                $data = SmsLog::latest();
            } else {
                $data = SmsLog::whereDate('created_at', today())->latest();
            }
            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('created_at', function (SmsLog $log) {
                    return timeFormat($log->created_at);
                })
                ->editColumn('message', function (SmsLog $log) {
                    return '<textarea class="form-control" rows="5" readonly>' . $log->message . '</textarea>';
                })
                ->rawColumns(['message'])
                ->make(true);
        }
    }
}
