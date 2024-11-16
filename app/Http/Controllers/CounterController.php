<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Game;
use App\Models\Counter;
use App\Services\Export;
use App\Models\CounterItem;
use App\Exports\ExcelExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class CounterController extends Controller
{

    public function crud(Request $request, Counter $counter)
    {
        $counter = new Counter;
        $action = $request->action;
        $id = $request->id;
        if ($action == "create") {
            return $counter->crud("create");
        } else if ($action == "update") {
            return $counter->crud("update", $id);
        }
    }

    public function index()
    {
        $counter = new Counter;
        return view('counter.index', compact('counter'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required',
            'minute' => 'required|numeric|min:1|max:2147483647',
        ], [], [
            'minute' => 'مقدار'
        ]);

        $action = $request->action;
        $id = $request->id;
        $name = $request->name;
        $minute = $request->minute;

        if ($action == "create") {
            $counter = Counter::create([
                'name' => $name,
                'min' => $minute
            ]);
        } else if ($action == "update") {
            $counter = Counter::find($id);
            $counter->name = $name;
            $counter->min = $minute;
            $counter->save();
        }
        return $counter->showIndex();
    }

    public function delete(Request $request)
    {
        $id = $request->id;
        $counter = Counter::find($id);
        $counter->delete();
        return $counter->showIndex();
    }

    public function export()
    {
        $counters = Counter::all();
        return Export::export($counters, ['name', 'min']);
    }

    public function board()
    {
        $counter = new Counter;
        $res = Game::where("status", 0)->whereNot("counter_id", 0)->get();
        return view('counter.board', compact('counter', 'res'));
    }
    public function boardV2(Request $request)
    {
        $max = CounterItem::max('updated_at');
        if ($request->type == 'check') {
            if ($request->last_update != $max) {
                return ['order' => 'refresh'];
            }
            return ['order' => 'false'];
        }elseif($request->type=='remove'){
            $counter=CounterItem::find($request->id);
            $counter->delete();
        }
        $counterItems = CounterItem::where('end', 0)->latest()->get();
        $last_update = $max;
        return view('counter-v2.board', compact('counterItems', 'last_update'));
    }

    public function storePassedTime(Request $request)
    {
        $game = Game::find($request->id);
        if ($game->status == 1) {
            return [
                'end' => true
            ];
        } else {
            $game->counter_passed = $request->passed;
            $game->save();
            return [
                'min' => $game->counter_min
            ];
        }
    }

    public function newPresents(Request $request)
    {
        parse_str($request->ids, $ids);
        $games = Game::where("status", 0)->whereNot("counter_id", 0)
            ->whereNotIn("id", $ids)->get();
        $counter = new Counter;
        return $counter->presentsInTimer($games);
    }

    public function edit(Request $request)
    {
        $id = $request->id;
        $counter = new Counter;

        return $counter->edit($id);
    }

    public function update(Request $request)
    {
        // $sign = $request->sign;
        if ($request->type == 'update') {
            $new_min = $request->action_value;
            $counterItem = CounterItem::find($request->id);
            $counterItem->update(
                ['min_duration' =>  $new_min]
            );
        } else {
            $game = Game::find($request->id);
            $counter = Counter::find($request->action_value);
            $game->counter()->create([
                'min_duration' => $counter->min,
                'title' => $game->person_fullname,
                'start_date' => Carbon::create($game->in_date . ' ' . $game->in_time)
            ]);
        }
        return response()->json([
            'message' => 'با موفقیت تغییر یافت.',
        ]);
    }

    public function dataTable(Request $request)
    {
        if ($request->ajax()) {
            $data = Counter::query();
            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('min', '{{ cnf($min) }}')
                ->addColumn('action', function (Counter $counter) {
                    $actionBtn = '';
                    if (Gate::allows('update')) {
                        $actionBtn .= '<button type="button" class="btn btn-warning btn-sm crud" data-action="update" data-id="' . $counter->id . '" data-bs-toggle="modal" data-bs-target="#crud"><i class="bx bx-edit"></i></button>';
                    }
                    if (Gate::allows('update')) {
                        $actionBtn .= '
                        <button class="btn btn-danger btn-sm delete-counter" data-id="' . $counter->id . '"><i class="bx bx-trash"></i></button>';
                    }
                    return $actionBtn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
    }
}
