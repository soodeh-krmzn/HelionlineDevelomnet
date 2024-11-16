<?php

namespace App\Http\Controllers;

use App\Models\ProductCategory;
use Illuminate\Http\Request;

class ProductCategoryController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required',
            'parent_id' => 'nullable|integer|exists:product_categories,id',
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
            $category = ProductCategory::create([
                'name' => $name,
                'parent_id' => $parent_id ?? 0,
                'status' => $status,
                'order' => $order ?? 0,
                'details' => $details,
            ]);
        } else if ($action == "update") {
            $category = ProductCategory::find($id);
            $category->name = $name;
            $category->parent_id = $parent_id ?? 0;
            $category->status = $status;
            $category->order = $order ?? 0;
            $category->details = $details;
            $category->save();
        }
        return $category->showIndex();
    }

    public function crud()
    {
        $category = new ProductCategory;
        return $category->crud();
    }

    public function delete(Request $request)
    {
        $id = $request->id;
        $category = ProductCategory::find($id);
        $category->delete();
        return $category->showIndex();
    }
}
