<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Services\Export;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required',
            'parent_id' => 'nullable|integer|exists:categories,id',
            'status' => 'nullable|integer',
            'order' => 'nullable|integer',
        ]);

        $id = $request->id;
        $action = $request->action;
        $name = $request->name;
        $parent_id = $request->parent_id;
        $status = $request->status;
        $order = $request->order;
        $details = $request->details;

        if ($action == "create") {
            $category = Category::create([
                'name' => $name,
                'parent_id' => $parent_id ?? 0,
                'status' => $status,
                'order' => $order ?? 0,
                'details' => $details,
            ]);
        } else if ($action == "update") {
            $category = Category::find($id);
            $category->name = $name;
            $category->parent_id = $parent_id ?? 0;
            $category->status = $status;
            $category->order = $order ?? 0;
            $category->details = $details;
            $category->save();
        }
        return $category->crud();
    }

    public function crud()
    {
        $category = new Category;
        return $category->crud();
    }

    public function delete(Request $request)
    {
        $id = $request->id;
        $category = Category::find($id);
        $category->delete();
        return $category->crud();
    }

    public function export()
    {
        return Export::export(Category::all());
    }
}
