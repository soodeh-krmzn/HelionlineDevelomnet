<?php

namespace App\Http\Controllers;

use App\Models\UserActivity;
use App\Services\Export;
use Carbon\Carbon;
use Hekmatinasser\Verta\Verta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Yajra\DataTables\Facades\DataTables;

class UserActivityController extends Controller
{
    public function index()
    {
        $activity = new UserActivity;
        return view('user-activity.index', compact('activity'));
    }

    public function crud()
    {
        $activity = new UserActivity;
        return $activity->crud();
    }

    public function store(Request $request)
    {
        $id = $request->id;
        $user_id = $request->user_id;
        $action = $request->action;

        if ($action == "in") {
            $check = UserActivity::where('user_id', $user_id)->where('out', null)->first();
            if ($check) {
                return response()->json([
                    'message' => 'ورود تکراری!'
                ], 400);
            }

            $activity = UserActivity::create([
                'user_id' => $user_id,
                'in' => now(),
                'out' => null
            ]);
        } else if ($action == "out") {
            $activity = UserActivity::find($id);
            if (!$activity) {
                return response()->json([
                    'message' => 'یافت نشد.'
                ], 404);
            }

            $activity->out = now();
            $activity->save();
        }

        return $activity->list();
    }

    public function crud2(Request $request)
    {
        $activity = new UserActivity;
        $action = $request->action;
        $id = $request->id;
        return $activity->crud2($action, $id);
    }

    public function store2(Request $request)
    {
        $action = $request->action;
        $id = $request->id;
        $user_id = $request->user_id;
        try {
            $in = Carbon::parse(dateTimeSetFormat($request->in));
            $out = Carbon::parse(dateTimeSetFormat($request->out));
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'لطفا تاریخ و ساعت را مطابق نمونه وارد کنید. (1401/01/01 00:00:00)'
            ], 400);
        }
        if ($in >= $out) {
            return response()->json([
                'message' => 'زمان خروج نمیتواند قبل از زمان ورود باشد.'
            ], 400);
        }

        $check1 = UserActivity::whereNot('id', $id)
                ->where('user_id', $user_id)
                ->where('in', '<', $in)
                ->where('out', '>', $in)
                ->first();
        $check2 = UserActivity::whereNot('id', $id)
                ->where('user_id', $user_id)
                ->where('in', '<', $out)
                ->where('out', '>', $out)
                ->first();
        $check3 = UserActivity::whereNot('id', $id)
                ->where('user_id', $user_id)
                ->where('in', '>=', $in)
                ->where('out', '<=', $out)
                ->first();
        if ($check1 || $check2 || $check3) {
            return response()->json([
                'message' => 'بازه زمانی وارد شده تکراری است.'
            ], 400);
        }

        if ($action == "create") {
            $activity = UserActivity::create([
                'user_id' => $user_id,
                'in' => $in,
                'out' => $out
            ]);
        } else if ($action == "update") {
            $activity = UserActivity::find($id);

            $activity->user_id = $user_id;
            $activity->in = $in;
            $activity->out = $out;
            $activity->save();
        }

        return $activity->showIndex();
    }

    public function delete(Request $request)
    {
        $id = $request->id;
        $activity = UserActivity::find($id);
        $activity->delete();
        return $activity->showIndex();
    }

    public function search(Request $request)
    {
        $user_id = $request->user_id;
        if (!is_null($request->from_date)) {
            $from_date = Carbon::parse(dateSetFormat($request->from_date));
        } else {
            $from_date = null;
        }
        if (!is_null($request->to_date)) {
            $to_date = Carbon::parse(dateSetFormat($request->to_date,1));
        } else {
            $to_date = null;
        }
        //  dd($user_id,$from_date, $to_date);
        $activities = UserActivity::query();

        if ($user_id != null) {
            $activities->where('user_id', $user_id);
        }
        if ($from_date != null) {
            $activities->where('in', '>=', $from_date);
        }
        if ($to_date != null) {
            $activities->where('in', '<=', $to_date);
        }

        // $activities = $activities->latest();
        return $activities;
    }

    public function export(Request $request)
    {
        $data = $this->search($request)->get();
        return Export::export($data->map(function($item){
            $item->user_name=$item->user?->getFullName();
            $item->in_date=dateToLangExcel($item->in);
            $item->out_date=dateToLangExcel($item->out);
            return $item;
        }),['user_name','in_date','out_date']);
    }

    public function dataTable(Request $request)
    {
        if ($request->ajax()) {
            if ($request->has('filter_search')) {
                $data = $this->search($request);
            } else {
                $data = UserActivity::query();
            }
            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('in', function(UserActivity $activity) {
                    return timeFormat($activity->in);
                })
                ->editColumn('out', function(UserActivity $activity) {
                    return timeFormat($activity->out);
                })
                ->editColumn('name', function(UserActivity $activity) {
                    return $activity->user?->getFullName() ?? '<span class="badge bg-danger">'.__("یافت نشد").'</span>';
                })
                ->addColumn('minutes', function(UserActivity $activity) {
                    $a = UserActivity::where('id', $activity->id)->get();
                    return $activity->minutes($a);
                })
                ->addColumn('action', function(UserActivity $activity) {
                    $actionBtn = '';
                    if (Gate::allows('update')) {
                        $actionBtn .= '<button type="button" class="btn btn-warning btn-sm crud" data-action="update" data-id="' . $activity->id . '" data-bs-target="#crud" data-bs-toggle="modal"><i class="bx bx-edit"></i></button>';
                    }
                    if (Gate::allows('delete')) {
                        $actionBtn .= '
                        <button type="button" class="btn btn-danger btn-sm delete-activity" data-id="' . $activity->id . '"><i class="bx bx-trash"></i></button>';
                    }
                    return $actionBtn;
                })
                ->rawColumns(['action', 'name'])
                ->make(true);
        }
    }

    public function getMinutes(Request $request)
    {
        if ($request->has('filter_search')) {
            $data = $this->search($request)->get();
        } else {
            $data = UserActivity::latest()->get();
        }

        if ($data->count() <= 0) {
            return '0';
        }

        $activity = new UserActivity;
        return $activity->minutes($data);
    }
}
