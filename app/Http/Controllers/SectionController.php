<?php

namespace App\Http\Controllers;

use App\Exports\ExcelExport;
use App\Models\Game;
use App\Models\Section;
use App\Models\Setting;
use App\Services\Export;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class SectionController extends Controller
{
    public function crud(Request $request, Section $section)
    {
        $section = new Section;
        $action = $request->action;
        if($action == "create") {
            return $section->crud("create");
        } else if ($action == "update") {
            $id = $request->id;
            return $section->crud("update", $id);
        }
    }

    public function index()
    {
        $section = new Section;
        return view('section.index', compact('section'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required',
            'show_status' => 'required'
        ]);

        $name = $request->name;
        $show_status = $request->show_status;
        $details = $request->details;
        $action = $request->action;
        $id = $request->id;

        if ($action == "create") {
            $section = Section::create([
                'name' => $name,
                'show_status' => $show_status,
                'details' => $details
            ]);
        } else if ($action == "update") {
            $section = Section::find($id);
            $section->name = $name;
            $section->details = $details;
            $section->show_status = $show_status;
            $section->save();
        }
        return $section->showIndex();
    }

    public function delete(Request $request)
    {
        $id = $request->id;
        $section = Section::find($id);
        $check = Game::where('section_id', $id)->get();
        if ($check) {
            return response()->json([
                'message' => __('بخش مورد نظر قابل حذف نمیباشد. زیرا در این بخش ورود ثبت شده است.')
            ], 500);
        }
        $section->delete();
        return $section->showIndex();
    }

    public function export()
    {
        $sections = Section::all();
        return Export::export($sections,['name','details']);
    }

    public function dataTable(Request $request)
    {
        if ($request->ajax()) {
            $data = Section::query();
            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('status', function(Section $section) {
                    return $section->status();
                })
                ->addColumn('action', function(Section $section) {
                    $actionBtn = '';
                    if (Gate::allows('update')) {
                        $actionBtn .= '<button type="button" class="btn btn-warning btn-sm crud" data-action="update" data-id="' . $section->id . '" data-bs-target="#crud-modal" data-bs-toggle="modal"><i class="bx bx-edit"></i></button>';
                    }
                    if (Gate::allows('delete')) {
                        $actionBtn .= '
                        <button type="button" class="btn btn-danger btn-sm delete-section" data-id="' . $section->id . '"><i class="bx bx-trash"></i></button>';
                    }
                    return $actionBtn;
                })
                ->rawColumns(['action', 'status'])
                ->make(true);
        }
    }

    public function updateDefaultSection(Request $request)
    {
        $section = Section::find($request->id);
        if ($request->status == "true") {
            Setting::updateOrCreate([
                'meta_key' => 'default_section'
            ], [
                'meta_value' => $request->id
            ]);
        } else {
            $setting = Setting::where('meta_key', 'default_section')->first();
            if ($setting) {
                $setting->delete();
            }
        }
    }

}
