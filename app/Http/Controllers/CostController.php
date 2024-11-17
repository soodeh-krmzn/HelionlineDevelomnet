<?php

namespace App\Http\Controllers;

use App\Models\Cost;
use App\Models\CostCategory;
use App\Services\Export;
use Carbon\Carbon;
use Hekmatinasser\Verta\Facades\Verta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Yajra\DataTables\Facades\DataTables;

class CostController extends Controller
{
    public function index()
    {
        $cost = new Cost;
        $categories = CostCategory::all();
        return view('cost.index', compact('cost', 'categories'));
    }

    public function crud(Request $request)
    {
        $cost = new Cost;
        $action = $request->action;
        $id = $request->id;
        return $cost->crud($action, $id);
    }

    public function store(Request $request)
    {
        $action = $request->action;
        $id = $request->id;
        $price = $request->price;
        $details = $request->details;
        $categories = $request->categories;

        if ($action == "create") {
            $cost = Cost::create([
                'created_by' => auth()->id(),
                'price' => $price,
                'details' => $details,
                'date'=>dateSetFormat($request->date)
            ]);
        } else if ($action == "update") {
            $cost = Cost::find($id);
            $cost->price = $price;
            $cost->details = $details;
            $cost->date=dateSetFormat($request->date);
            $cost->save();
        }
        $cost->categories()->sync($categories);
        return $cost->showIndex();
    }

    public function delete(Request $request)
    {
        $cost = Cost::find($request->id);
        $cost->delete();

        return $cost->showIndex();
    }

    public function search(Request $request)
    {
        $user_id = $request->user_id;
        $from_price = $request->from_price;
        $to_price = $request->to_price;
        $category_id = $request->category_id;
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

        $costs = Cost::query()->select("costs.*");

        if ($user_id != null) {
            $costs->where('created_by', $user_id);
        }
        if ($from_price != null) {
            $costs->where('price', '>=', $from_price);
        }
        if ($to_price != null) {
            $costs->where('price', '<=', $to_price);
        }
        if ($from_date != null) {
            $costs->where('costs.date', '>=', $from_date);
        }
        if ($to_date != null) {
            $costs->where('costs.date', '<=', $to_date);
        }
        if ($category_id != null) {
            $costs->join('cost_category', 'costs.id', '=', 'cost_category.cost_id')
                    ->where('cost_category.category_id', $category_id);
        }

        $costs = $costs->orderBy('costs.date', 'desc');
        return $costs;
    }

    public function dataTable(Request $request)
    {
        if ($request->ajax()) {
            if ($request->has('filter_search')) {
                $data = $this->search($request);
            } else {
                $data = Cost::query();
            }
            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('date', function(Cost $cost) {
                    return timeFormat($cost->date);
                })
                ->editColumn('price', '{{ cnf($price) }}')
                ->editColumn('created_by', function(Cost $cost) {
                    return $cost->user?->getFullName() ?? '<span class="badge bg-danger">'.__("یافت نشد").'</span>';
                })
                ->addColumn('categories', function(Cost $cost){
                    $categories = '';
                    foreach ($cost->categories as $category){
                        $categories .= $category->name . ' , ';
                    }
                    return $categories;
                })
                ->addColumn('action', function(Cost $cost) {
                    $actionBtn = '';
                    if (Gate::allows('update')) {
                        $actionBtn .= '<button type="button" class="btn btn-warning btn-sm crud" data-action="update" data-id="' . $cost->id . '" data-bs-toggle="offcanvas" data-bs-target="#offcanvasAddUser"><i class="bx bx-edit"></i></button>';
                    }
                    if (Gate::allows('delete')) {
                        $actionBtn .= '
                        <button class="btn btn-danger btn-sm delete-cost" data-id="' . $cost->id . '"><i class="bx bx-trash"></i></button>';
                    }
                    return $actionBtn;
                })
                ->rawColumns(['action', 'user','created_by'])
                ->make(true);
        }
    }

    public function getSum(Request $request)
    {
        if ($request->has('filter_search')) {
            $data = $this->search($request);
        } else {
            $data = Cost::latest()->get();
        }

        if ($data->count() <= 0) {
            return 0;
        }
        return $data->sum('price');
    }

    public function export(Request $request)
    {
        $data = $this->search($request)->get();
        return Export::export($data->map(function($item){
            $item->user_name=$item->user?->getFullName();
            $item->price=price($item->price);
            $item->categories=$item->categoriesInString();
            return $item;
        }),['user_name','categories','price','details']);
    }

}
