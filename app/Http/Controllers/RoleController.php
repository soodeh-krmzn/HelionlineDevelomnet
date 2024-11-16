<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;

class RoleController extends Controller
{

    public function crud(Request $request, Role $role)
    {
        $role = new Role;
        $action = $request->action;
        $id = $request->id;
        if($action == "create") {
            return $role->crud("create");
        } else if ($action == "update") {
            return $role->crud("update", $id);
        }
    }

    public function index()
    {
        $role = new Role;
        return view('role.index', compact('role'));
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

        if($action == "create") {
            $role = Role::create([
                'name' => $name,
                'details' => $details
            ]);
        } else if ($action == "update") {
            $role = Role::find($id);
            $role->name = $request->name;
            $role->details = $request->details;
            $role->save();
        }
        return $role->showIndex();
    }

    public function delete(Request $request)
    {
        $id = $request->id;
        $role = Role::find($id);
        $role->delete();
        return $role->showIndex();
    }

}
