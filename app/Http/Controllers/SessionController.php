<?php

namespace App\Http\Controllers;

use App\Exports\ExcelExport;
use App\Models\Course;
use App\Models\Session;
use App\Services\Export;
use Hekmatinasser\Verta\Facades\Verta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class SessionController extends Controller
{
    public function index(Request $request)
    {
        $session = new Session;
        $course = Course::find($request->course_id);
        if (!$course) {
            return abort(404);
        }
        return view('session.index', compact('session', 'course'));
    }

    public function crud(Request $request)
    {
        $id = $request->id;
        $action = $request->action;
        $course_id = $request->course_id;
        $session = new Session;

        return $session->crud($action, $id, $course_id);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'course_id' => 'required|exists:courses,id',
            'date' => 'required'
        ]);

        $id = $request->id;
        $action = $request->action;
        $course_id = $request->course_id;
        $date = dateSetFormat($request->date);
        $details = $request->details;
        parse_str($request->persons, $person_array);
        $persons = array_keys($person_array, 1);

        if ($action == "create") {
            $session = Session::create([
                'course_id' => $course_id,
                'date' => $date,
                'details' => $details
            ]);
        } else if ($action == "update") {
            $session = Session::find($id);

            $session->course_id = $course_id;
            $session->date = $date;
            $session->details = $details;
            $session->save();
        }

        $pivot = array_fill(0, count($persons), ['course_id' => $course_id]);
        $syncData = array_combine($persons, $pivot);

        $session->people()->sync($syncData);

        return $session->showIndex($session->course);
    }

    public function delete(Request $request)
    {
        $session = Session::find($request->id);
        $session->delete();
        return $session->showIndex($session->course);
    }

    public function export()
    {
        return Export::export(Session::where('course_id',request('course_id'))->latest()->get()->map(function($item){
            $item['course']=$item->course->name;
            $item['holding_date']=dateToLangExcel($item->date);
            return $item;
        }),['course','holding_date','details']);
    }

    public function dataTable(Request $request)
    {
        if ($request->ajax()) {
            $data = Session::where('course_id',request('course_id'))->latest()->get();
            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('date', function(Session $session) {
                    return DateFormat($session->date);
                })
                ->addColumn('action', function(Session $session) {
                    $actionBtn = '';
                    if (Gate::allows('update')) {
                        $actionBtn .= '<button type="button" class="btn btn-warning btn-sm crud" data-action="update" data-id="' . $session->id . '" data-bs-target="#crud" data-bs-toggle="modal"><i class="bx bx-edit"></i></button>';
                    }
                    if (Gate::allows('delete')) {
                        $actionBtn .= '
                        <button type="button" class="btn btn-danger btn-sm delete-session" data-id="' . $session->id . '"><i class="bx bx-trash"></i></button>';
                    }
                    return $actionBtn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
    }
}
