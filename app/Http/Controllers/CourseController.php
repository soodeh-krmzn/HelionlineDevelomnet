<?php

namespace App\Http\Controllers;

use App\Exports\ExcelExport;
use App\Models\Course;
use App\Models\Payment;
use App\Models\Person;
use App\Models\Wallet;
use App\Services\Export;
use Hekmatinasser\Verta\Facades\Verta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class CourseController extends Controller
{
    public function index()
    {
        $course = new Course;
        return view('course.index', compact('course'));
    }

    public function crud(Request $request)
    {
        $id = $request->id;
        $action = $request->action;
        $course = new Course;

        return $course->crud($action, $id);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required',
            'user_id' => 'required',
            'price' => 'nullable|numeric',
            'capacity' => 'nullable|integer',
            'sessions' => 'nullable|integer'
        ]);

        $id = $request->id;
        $action = $request->action;
        $name = $request->name;
        $user_id = $request->user_id;
        $price = $request->price;
        $capacity = $request->capacity;
        $sessions = $request->sessions;
        $details = $request->details;
        if ($request->start != null) {
            $start = dateSetFormat($request->start);
        } else {
            $start = null;
        }

        if ($action == "create") {
            $course = Course::create([
                'name' => $name,
                'user_id' => $user_id,
                'price' => $price,
                'sessions' => $sessions,
                'start' => $start,
                'capacity' => $capacity,
                'details' => $details
            ]);
        } else if ($action == "update") {
            $course = Course::find($id);
            if (!$course) {
                return response()->json([
                    'message' => 'کلاس مورد نظر یافت نشد.'
                ], 404);
            }

            $course->name = $name;
            $course->user_id = $user_id;
            $course->price = $price;
            $course->sessions = $sessions;
            $course->start = $start;
            $course->capacity = $capacity;
            $course->details = $details;
            $course->save();
        }

        return $course->showIndex();
    }

    public function delete(Request $request)
    {
        $course = Course::find($request->id);
        $course->delete();
        return $course->showIndex();
    }

    public function showRegister(Request $request)
    {
        $course = Course::find($request->id);
        if (!$course) {
            return response()->json([
                'message' => 'کلاس یافت نشد.'
            ], 404);
        }

        return $course->showRegister();
    }

    public function syncPerson(Request $request)
    {
        $validated = $request->validate([
            'id' => 'required|exists:courses,id',
            'person_id' => 'required|exists:people,id',
        ]);

        $action = $request->action;
        $id = $request->id;
        $person_id = $request->person_id;
        parse_str($request->payments, $payments);
        parse_str($request->details, $details);
        $balance = 0;

        $course = Course::find($id);
        $person = Person::find($person_id);

        if ($action == "add") {
            if ($course->people->contains($person_id)) {
                return response()->json([
                    'message' => __('این شخص قبلا در این کلاس ثبت نام کرده است.')
                ], 400);
            }

            if ($course->people?->count() >= $course->capacity) {
                return response()->json([
                    'message' => __('ظرفیت این کلاس به اتمام رسیده است.')
                ], 400);
            }

            $course->people()->attach($person_id);

            Payment::create([
                "user_id" => auth()->id(),
                "person_id" => $person_id,
                "price" => $course->price * (-1),
                "details" => "مبلغ ثبت نام کلاس " . $course->name,
                "type" => "course",
                "object_type" => "App\Models\Course",
                "object_id" => $course->id
            ]);
            $balance -= $course->price;

            $pay = Payment::storeAll($payments, $details, $person, "App\Models\Course", $course->id, "پرداخت کلاس کد " . $course->id);

            $balance += $pay['balance'];

            $person->balance += $balance;
            $person->save();
        } else if ($action == "remove") {
            $course->people()->detach($person_id);

            Payment::create([
                "user_id" => auth()->id(),
                "person_id" => $person_id,
                "price" => $course->price,
                "details" => "انصراف از کلاس " . $course->name,
                "type" => "course",
                "object_type" => "App\Models\Course",
                "object_id" => $course->id
            ]);
            $balance += $course->price;

            $person->balance += $balance;
            $person->save();
        }

        return $course->showPeople();
    }

    public function showPeople(Request $request)
    {
        $course = Course::find($request->id);
        return $course->showPeople();
    }

    public function export()
    {
        return Export::export(Course::latest()->get()->map(function ($item){
            $item['teacher']=$item->user?->getFullName();
            $item['price_format']=price($item->price);
            $item['strat_date']=app()->getLocale()=='fa'?persianTime($item['start']):gregorianTime($item['start']);
            return $item;
        }),['name','teacher','strat_date','sessions','capacity','price_format','details']);
    }

    public function dataTable(Request $request)
    {
        if ($request->ajax()) {
            $data = Course::latest()->get();
            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('start', '{{ DateFormat($start)}}')
                ->editColumn('price', '{{ cnf($price) }}')
                ->editColumn('capacity', '{{ cnf($capacity) }}')
                ->addColumn('user', function(Course $course) {
                    return $course->user?->getFullName() ?? '<span class="badge bg-danger">'.__("یافت نشد").'</span>';
                })
                ->addColumn('action', function(Course $course) {
                    $actionBtn = '';
                    if (Gate::allows('update')) {
                        $actionBtn .= '
                        <button type="button" class="btn btn-success btn-sm register-course" data-id="' . $course->id .'" data-bs-target="#crud" data-bs-toggle="modal"><span>'.__("ثبت نام").'</span></button>
                        <button type="button" class="btn btn-warning btn-sm crud" data-action="update" data-id="' . $course->id .'" data-bs-target="#crud" data-bs-toggle="modal"><i class="bx bx-edit"></i></button>';
                    }
                    if (Gate::allows('delete')) {
                        $actionBtn .= '
                        <button type="button" class="btn btn-danger btn-sm delete-course" data-id="' . $course->id . '"><i class="bx bx-trash"></i></button>';
                    }
                    $actionBtn .= '
                                  <button type="button" class="btn btn-primary btn-sm people-course" data-id="' . $course->id . '" data-bs-target="#people" data-bs-toggle="modal">'.__("افراد").'</button>
                                  <a href="' . route("session", ["course_id" => $course->id]) . '" class="btn btn-info btn-sm" data-id="' . $course->id . '">'.__("جلسات").'</a>';
                    return $actionBtn;
                })
                ->rawColumns(['action', 'gender', 'user'])
                ->make(true);
        }
    }
}
