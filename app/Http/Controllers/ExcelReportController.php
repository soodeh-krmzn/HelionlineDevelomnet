<?php

namespace App\Http\Controllers;

use App\Models\ExcelReport;
use App\Models\User;
use Carbon\Carbon;
use Hekmatinasser\Verta\Facades\Verta;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class ExcelReportController extends Controller
{
    public function index()
    {
        $users = User::getUsers();
        return view('excel-report.index', compact('users'));
    }

    public function search(Request $request)
    {
        $table = $request->table;
        $user_id = $request->user_id;

        if (!is_null($request->from_date)) {
            $from_date = Carbon::parse(dateSetFormat($request->from_date));
        } else {
            $from_date = null;
        }
        if (!is_null($request->to_date)) {
            $to_date = Carbon::parse(dateSetFormat($request->to_date,1));
        } else {
            $to_date = null;
        }

        $reports = ExcelReport::query();

        if ($user_id != null) {
            $reports->where('user_id', $user_id);
        }
        if ($table != null) {
            $table->where('table', $table);
        }
        if ($from_date != null) {
            $reports->where('created_at', '>=', $from_date);
        }
        if ($to_date != null) {
            $reports->where('created_at', '<=', $to_date);
        }

        $reports = $reports->latest()->get();
        return $reports;
    }

    public function dataTable(Request $request)
    {
        if ($request->ajax()) {
            if ($request->has('filter_search')) {
                $data = $this->search($request);
            }elseif($request->all){
                $data = ExcelReport::query();
            } else {
                $data = ExcelReport::whereDate('created_at',today());
            }
            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('created_at', function(ExcelReport $report) {
                    return timeFormat($report->created_at);
                })
                ->editColumn('user_id', function(ExcelReport $report) {
                    return $report->user?->getFullName() ?? '<span class="badge bg-danger">'.__("یافت نشد").'</span>';
                })
                ->editColumn('link', function(ExcelReport $report) {
                    $btn = '<a href="' . $report->link . '" class="btn btn-success btn-sm"><i class="bx bx-download"></i></a>';
                    return $btn;
                })
                ->rawColumns(['link', 'user'])
                ->make(true);
        }
    }

}
