<?php

namespace App\Http\Controllers;

use App\Models\Admin\SmsPattern;
use App\Models\Admin\SmsPatternCategory;
use App\Models\Setting;
use Illuminate\Http\Request;

class SmsPatternController extends Controller
{

    public function crud(Request $request, SmsPattern $smsPattern)
    {
        $smsPatternCategory = new SmsPatternCategory;
        $smsPattern = new SmsPattern;
        $action = $request->action;
        if($action == "create") {
            return $smsPattern->crud("create", 0, $smsPatternCategory);
        } else if ($action == "update") {
            $id = $request->id;
            return $smsPattern->crud("update", $id, $smsPatternCategory);
        }
    }


    public function index()
    {
        $smsPattern = new SmsPattern;
        return view('sms-pattern.index', compact('smsPattern'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'text' => 'required',
            'page' => 'required',
            'cost' => 'required'
        ]);

        $action = $request->action;
        $id = $request->id;
        $category_id = $request->category_id;
        $text = $request->text;
        $page = $request->page;
        $cost = $request->cost;

        if ($action == "create") {
            $smsPattern = SmsPattern::create([
                'category_id' => $category_id,
                'text' => $text,
                'page' => $page,
                'cost' => $cost
            ]);
        } else if ($action == "update") {
            $smsPattern = SmsPattern::find($id);
            $smsPattern->category_id = $category_id;
            $smsPattern->text = $text;
            $smsPattern->page = $page;
            $smsPattern->cost = $cost;
            $smsPattern->save();
        }
        return $smsPattern->showIndex();
    }

    public function delete(Request $request)
    {
        $id = $request->id;
        $smsPattern = SmsPattern::find($id);
        $smsPattern->delete();
        return $smsPattern->showIndex();
    }

    public function status(Request $request)
    {
        $smsPattern = SmsPattern::find($request->id);
        $category = $smsPattern->category->name;
        if ($request->status == "true") {
            Setting::updateOrCreate([
                'meta_key' => $category
            ], [
                'meta_value' => $smsPattern->id
            ]);
        } else {
            $setting = Setting::where('meta_key', $category)->first();
            if ($setting) {
                $setting->delete();
            }
        }
        return $smsPattern->showIndex1();
    }

}
