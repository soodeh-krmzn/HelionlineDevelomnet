<?php

namespace App\Http\Controllers;

use App\Models\Admin\SmsPatternCategory;
use Illuminate\Http\Request;

class SmsPatternCategoryController extends Controller
{

    public function crud(Request $request, SmsPatternCategory $smsPatternCategory)
    {
        $smsPatternCategory = new SmsPatternCategory;
        $action = $request->action;
        if($action == "create") {
            return $smsPatternCategory->crud("create");
        } else if ($action == "update") {
            $id = $request->id;
            return $smsPatternCategory->crud("update", $id);
        }
    }

    public function index()
    {
        $smsPatternCategory = new SmsPatternCategory;
        return view('sms-pattern-category.index', compact('smsPatternCategory'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required'
        ]);

        $action = $request->action;
        $id = $request->id;
        $name = $request->name;

        if ($action == "create") {
            $smsPatternCategory = SmsPatternCategory::create([
                'name' => $name
            ]);
        } else if ($action == "update") {
            $smsPatternCategory = SmsPatternCategory::find($id);
            $smsPatternCategory->name = $name;
            $smsPatternCategory->save();
        }
        return $smsPatternCategory->showIndex();
    }

    public function delete(Request $request)
    {
        $id = $request->id;
        $smsPatternCategory = SmsPatternCategory::find($id);
        $smsPatternCategory->delete();
        return $smsPatternCategory->showIndex();
    }

}
