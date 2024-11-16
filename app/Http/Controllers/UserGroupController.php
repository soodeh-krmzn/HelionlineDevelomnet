<?php

namespace App\Http\Controllers;

use App\Models\UserGroup;
use App\Models\UserGroupPermission;
use App\Services\Export;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Yajra\DataTables\Facades\DataTables;

class UserGroupController extends Controller
{
    public function crud(Request $request, UserGroup $group)
    {
        $group = new UserGroup;
        $action = $request->action;
        $id = $request->id;
        if($action == "create") {
            return $group->crud("create");
        } else if ($action == "update") {
            return $group->crud("update", $id);
        }
    }

    public function index()
    {
        $group = new UserGroup;
        return view('user-group.index', compact('group'));
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
        parse_str($request->permissions, $permissions_array);
        $permissions = array_keys($permissions_array);

        if($action == "create") {
            $group = UserGroup::create([
                'name' => $name,
                'description' => $details
            ]);
        } else if ($action == "update") {
            $group = UserGroup::find($id);
            $group->name = $request->name;
            $group->description = $request->details;
            $group->save();
            foreach ($group->permissions as $p) {
                if (!in_array($p->permission, $permissions)) {
                    $p->delete();
                }
            }
        }
        foreach ($permissions as $p) {
            UserGroupPermission::updateOrCreate([
                'group_id' => $group->id,
                'permission' => $p
            ]);
        }
        return $group->showIndex();
    }

    public function delete(Request $request)
    {
        $id = $request->id;
        $group = UserGroup::find($id);
        $group->delete();
        return $group->showIndex();
    }

    public function showMenus(Request $request)
    {
        $id = $request->id;
        $group = new UserGroup;
        return $group->showMenus($id);
    }

    public function storeMenus(Request $request)
    {
        $id = $request->id;
        parse_str($request->menus, $menus);
        $m = [];

        if (array_key_exists('menus', $menus)) {
            $m = $menus['menus'];
        }

        $group = UserGroup::find($id);
        $group->menus()->sync($m);
    }

    public function export()
    {
        return Export::export(UserGroup::all()->map(function ($item) {
            $item->role=$item->name;
            return $item;
        }),['role','description']);
    }

    public function dataTable(Request $request)
    {
        if ($request->ajax()) {
            $data = UserGroup::latest()->get();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function(UserGroup $group) {
                    $actionBtn = '';
                    if (Gate::allows('update')) {
                        $actionBtn .= '
                        <button type="button" class="btn btn-info btn-sm menu-group" data-id="' . $group->id . '" data-bs-target="#menu" data-bs-toggle="modal"><i class="bx bx-menu"></i></button>
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
}
