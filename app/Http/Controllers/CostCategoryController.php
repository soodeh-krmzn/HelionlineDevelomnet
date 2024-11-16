<?php

namespace App\Http\Controllers;

use App\Models\CostCategory;
use Illuminate\Http\Request;

class CostCategoryController extends Controller
{
    public function crud()
    {
        $category = new CostCategory;
        return $category->crud();
    }

    public function store(Request $request)
    {
        $action = $request->action;
        $id = $request->id;
        $name = $request->name;
        $code = $request->code;
        $details = $request->details;
        $parent_id = $request->parent_id;
        $display_order = $request->display_order;

        if ($action == "create") {
            $category = CostCategory::create([
                'name' => $name,
                'code' => $code,
                'details' => $details,
                'parent_id' => $parent_id,
                'display_order' => $display_order
            ]);
        } else if ($action == "update") {
            $category = CostCategory::find($id);
            $category->name = $name;
            $category->code = $code;
            $category->details = $details;
            $category->parent_id = $parent_id;
            $category->display_order = $display_order;
            $category->save();
        }

        return $category->showIndex();
    }

    public function delete(Request $request)
    {
        $category = CostCategory::find($request->id);
        if ($category->costs->count() > 0) {
            return response()->json([
                'message' => 'در این دسته، هزینه تعریف شده است.'
            ], 409);
        }
        $category->delete();
        return $category->showIndex();
    }
}
