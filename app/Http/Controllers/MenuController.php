<?php

namespace App\Http\Controllers;

use App\Models\Admin\Menu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class MenuController extends Controller
{
    public function crud(Request $request, Menu $menu)
    {
        $menu = new Menu;
        $action = $request->action;
        $id = $request->id;
        if($action == "create") {
            return $menu->crud("create");
        } else if ($action == "update") {
            return $menu->crud("update", $id);
        }
    }

    public function index()
    {
        $menu = new Menu;
        return view('menu.index', compact('menu'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'label' => 'required',
            'icon' => 'required',
            'url' => 'required'
        ]);

        $action = $request->action;
        $id = $request->id;
        $parent_id = $request->parent_id;
        $label = $request->label;
        $icon = $request->icon;
        $url = $request->url;
        $learn_url = $request->learn_url;
        $display_order = $request->display_order;
        $details = $request->details;

        if ($action == "create") {
            $menu = Menu::create([
                'parent_id' => $parent_id,
                'name' => $label,
                'icon' => $icon,
                'url' => $url,
                'learn_url' => $learn_url,
                'display_order' => $display_order,
                'details' => $details
            ]);
        } else if ($action == "update") {
            $menu = Menu::find($id);
            $menu->parent_id = $request->parent_id;
            $menu->name = $request->label;
            $menu->icon = $request->icon;
            $menu->url = $request->url;
            $menu->learn_url = $request->learn_url;
            $menu->display_order = $request->display_order;
            $menu->details = $request->details;
            $menu->save();
        }
        return $menu->showIndex();
    }

    public function delete(Request $request)
    {
        $id = $request->id;
        $menu = Menu::find($id);
        $menu->delete();
        return $menu->showIndex();
    }

    public function getItems()
    {
        $response = Http::post('http://helisystem.ir/api/get-menu-items', [
            'name' => 'Steve',
            'role' => 'Network Administrator',
        ]);
        dd($response);
    }

    public function help(Request $request)
    {
        $validated = $request->validate([
            'url' => 'required'
        ]);

        $menu = new Menu;
        return $menu->getHelp($request->url);
    }

}
