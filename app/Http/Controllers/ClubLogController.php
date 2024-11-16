<?php

namespace App\Http\Controllers;

use App\Exports\ExcelExport;
use App\Models\ClubLog;
use App\Models\Person;
use App\Services\Export;
use Carbon\Carbon;
use Hekmatinasser\Verta\Facades\Verta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class ClubLogController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $log = new ClubLog;
        return view('club.logs', compact('log'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(ClubLog $clubLog)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ClubLog $clubLog)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ClubLog $clubLog)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ClubLog $clubLog)
    {
        //
    }

    public function search(Request $request)
    {
        $person_id = $request->person_id;
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

        $logs = ClubLog::query();

        if ($person_id != null) {
            $logs->where('person_id', $person_id);
        }
        if ($from_date != null) {
            $logs->where('created_at', '>=', $from_date);
        }
        if ($to_date != null) {
            $logs->where('created_at', '<=', $to_date);
        }
        return $logs;
    }

    public function export(Request $request)
    {
        $data = $this->search($request)->get();
        return Export::export($data->map(function($item){
            $item['person_name']=$item->person?->getFullName();
            $item['price_format']=price($item->price);
            return $item;
        }),['person_name','rate','balance_rate','price_format','description']);
    }

    public function dataTable(Request $request)
    {
        if ($request->ajax()) {
            if ($request->has('filter_search')) {
                $data = $this->search($request);
            } else {
                $data = ClubLog::query();
            }
            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('created_at', function(ClubLog $log) {
                    return timeFormat($log->created_at);
                })
                ->editColumn('price', '{{ cnf($price) }}')
                ->addColumn('person', function(ClubLog $log){
                    return $log->person?->getFullName() ?? '<span class="badge bg-danger">'.__("یافت نشد").'</span>';
                })
                ->rawColumns(['person_id'])
                ->make(true);
        }
    }
}
