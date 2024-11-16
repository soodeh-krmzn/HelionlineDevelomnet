<?php

namespace App\Http\Controllers;

use App\Exports\ExcelExport;
use App\Models\Station;
use App\Services\Export;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class StationController extends Controller
{

    public function crud(Request $request, Station $station)
    {
        $station = new Station;
        $action = $request->action;
        if($action == "create") {
            return $station->crud("create");
        } else if ($action == "update") {
            $id = $request->id;
            return $station->crud("update", $id);
        }
    }

    public function index()
    {
        $station = new Station;
        return view('station.index', compact('station'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required',
            'show_status' => 'required'
        ]);

        $action = $request->action;
        $id = $request->id;
        $name = $request->name;
        $show_status = $request->show_status;
        $details = $request->details;

        if ($action == "create") {
            $station = Station::create([
                'name' => $name,
                'show_status' => $show_status,
                'details' => $details
            ]);
        } else if ($action == "update") {
            $station = Station::find($id);
            $station->name = $name;
            $station->details = $details;
            $station->show_status = $show_status;
            $station->save();
        }
        return $station->showIndex();
    }

    public function delete(Request $request)
    {
        $id = $request->id;
        $station = Station::find($id);
        $station->delete();
        return $station->showIndex();
    }

    public function export()
    {
        return Export::export(Station::all()->map(function($item){
            $item['status_msg']=$item['show_status']==1?__("excel.active"):__("excel.deactive");
            return $item;
        }),['name','status_msg','details']);
    }

    public function dataTable(Request $request)
    {
        if ($request->ajax()) {
            $data = Station::query();
            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('status', function(Station $station) {
                    return $station->status();
                })
                ->addColumn('action', function(Station $station) {
                    $actionBtn = '';
                    if (Gate::allows('update')) {
                        $actionBtn .= '<button type="button" class="btn btn-warning btn-sm crud" data-action="update" data-id="' . $station->id . '" data-bs-target="#crud-modal" data-bs-toggle="modal"><i class="bx bx-edit"></i></button>';
                    }
                    if (Gate::allows('delete')) {
                        $actionBtn .= '
                        <button type="button" class="btn btn-danger btn-sm delete-station" data-id="' . $station->id . '"><i class="bx bx-trash"></i></button>';
                    }
                    return $actionBtn;
                })
                ->rawColumns(['action', 'status'])
                ->make(true);
        }
    }

}
