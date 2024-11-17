<?php

namespace App\Http\Controllers;

use App\Exports\ExcelExport;
use App\Models\Group;
use App\Services\Export;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class GroupController extends Controller
{
    public function crud(Request $request, Group $group)
    {
        $group = new Group;
        $action = $request->action;
        $id = $request->id;
        if ($action == "create") {
            return $group->crud("create");
        } else if ($action == "update") {
            return $group->crud("update", $id);
        }
    }

    public function index()
    {
        $group = new Group;
        return view('group.index', compact('group'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required'
        ]);

        $action = $request->action;
        $id = $request->id;
        $name = $request->name;
        $details = $request->details;

        if ($action == "create") {
            $group = Group::create([
                'name' => $name,
                'details' => $details
            ]);
        } else if ($action == "update") {
            $group = Group::find($id);
            $group->name = $request->name;
            $group->details = $request->details;
            $group->save();
        }
        return $group->showIndex();
    }

    public function delete(Request $request)
    {
        $id = $request->id;
        $group = Group::find($id);
        $group->delete();
        return $group->showIndex();
    }

    public function export()
    {
        return Export::export(Group::all(), ['name', 'details']);
    }
    public function exportPeople(Request $request) {
        // dd($request->all();)
        $data = Group::find($request->group_id)->people;
        return Export::export($data->map(function ($item) {
            $item['birth_date'] = dateToLangExcel($item->birth);
            return $item;
        }), ['id', 'name', 'family', 'birth_date', 'reg_code', 'mobile', 'national_code', 'sharj']);
    }
    public function dataTable(Request $request)
    {
        if ($request->ajax()) {
            $data = Group::query();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function (Group $group) {
                    $actionBtn = '';
                    if (Gate::allows('update')) {
                        $actionBtn .= '
                        <button type="button" class="btn btn-success btn-sm show-people" data-id="' . $group->id . '" data-bs-target="#people-modal" data-bs-toggle="modal"><i class="bx bx-user"></i></button>
                        <button type="button" class="btn btn-warning btn-sm crud" data-action="update" data-id="' . $group->id . '" data-bs-target="#crud" data-bs-toggle="modal"><i class="bx bx-edit"></i></button>';
                    }
                    if (Gate::allows('delete')) {
                        $actionBtn .= '
                        <button type="button" class="btn btn-danger btn-sm delete-group" data-id="' . $group->id . '"><i class="bx bx-trash"></i></button>';
                    }
                    return $actionBtn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
    }

    public function peopleForm(Request $request)
    {
        $group = Group::find($request->id);
        if (!$group) {
            return response()->json([
                'message' => 'یافت نشد.'
            ], 404);
        }
        return $group->peopleForm();
    }

    public function storePeople(Request $request)
    {
        $group = Group::find($request->id);
        if (!$group) {
            return response()->json([
                'message' => 'یافت نشد.'
            ], 404);
        }

        $group->people()->sync($request->people);
    }

    public function search(Request $request)
    {
        $name = $request->name;

        $groups = Group::query();

        if ($name != null) {
            $groups->where('name', 'like', '%' . $name . '%');
        }

        $groups = $groups->latest()->get();
        return $groups;
    }

    public function showPeople(Request $request)
    {
        $group = Group::find($request->id);
        if (!$group) {
            return response()->json([
                'message' => 'یافت نشد.'
            ], 404);
        }
        return $group->showPeople();
    }
}
