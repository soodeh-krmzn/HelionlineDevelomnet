<?php

namespace App\Http\Controllers;

use App\Exports\ExcelExport;
use App\Models\Adjective;
use App\Services\Database;
use App\Services\Export;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class AdjectiveController extends Controller
{
    public function crud(Request $request, Adjective $adjective)
    {
        $adjective = new Adjective;
        $action = $request->action;
        if($action == "create") {
            return $adjective->crud("create");
        } else if ($action == "update") {
            $id = $request->id;
            return $adjective->crud("update", $id);
        }
    }

    public function index()
    {
        $adjective = new Adjective;
        return view('adjective.index', compact('adjective'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required'
        ]);

        $name = $request->name;
        $action = $request->action;
        $id = $request->id;

        if ($action == "create") {
            $adjective = Adjective::create([
                'name' => $name
            ]);
        } else if ($action == "update") {
            $adjective = Adjective::find($id);
            $adjective->name = $name;
            $adjective->save();
        }
        return $adjective->showIndex();
    }

    public function delete(Request $request)
    {
        $id = $request->id;
        $adjective = Adjective::find($id);
        $adjective->delete();
        return $adjective->showIndex();
    }

    public function export()
    {
        return Export::export(Adjective::all(),['row','name']);
    }

    public function dataTable(Request $request)
    {
        if ($request->ajax()) {
            $data = Adjective::query();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function(Adjective $adjective){
                    $actionBtn = '';
                    if (Gate::allows('update')) {
                        $actionBtn .= '<button type="button" class="btn btn-warning btn-sm crud" data-action="update" data-id="' . $adjective->id . '" data-bs-toggle="modal" data-bs-target="#crud"><i class="bx bx-edit"></i></button>';
                    }
                    if (Gate::allows('delete')) {
                        $actionBtn .= '
                        <button class="btn btn-danger btn-sm delete-adjective" data-id="'.  $adjective->id . '"><i class="bx bx-trash"></i></button>';
                    }
                    return $actionBtn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
    }

}
