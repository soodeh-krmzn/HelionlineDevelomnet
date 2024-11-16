<?php

namespace App\Http\Controllers;

use App\Exports\ExcelExport;
use App\Models\EditReport;
use App\Models\Offer;
use App\Models\Setting;
use App\Services\Export;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class OfferController extends Controller
{
    public function crud(Request $request, Offer $offer)
    {
        $offer = new Offer;
        $action = $request->action;
        if ($action == "create") {
            return $offer->crud("create");
        } else if ($action == "update") {
            $id = $request->id;
            return $offer->crud("update", $id);
        }
    }

    public function index()
    {
        $setting = new Setting();
        if (request()->has('global_offer')) {
            $setting->updateOrCreate([
                'meta_key' => 'global_offer'
            ], [
                'meta_value' => request('global_offer') ? request('global_offer') : ''
            ]);
            return;
        }
        $offer = new Offer;
        $offers = Offer::latest()->get();
        return view('offer.index', compact('offer', 'offers', 'setting'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required',
            'type' => 'required',
            'per' => 'required|numeric',
            'min_price' => 'required|numeric',
        ]);

        if ($request->type == "درصد" && $request->per > 100) {
            return response()->json([
                'message' => 'میزان تخفیف نمیتواند بیشتر از صد درصد باشد.'
            ], 400);
        }

        $name = $request->name;
        $type = $request->type;
        $per = $request->per;
        $min_price = $request->min_price;
        $calc = $request->calc;
        $details = $request->details;
        $id = $request->id;
        $action = $request->action;

        if ($action == "create") {
            $offer = Offer::create([
                'name' => $name,
                'type' => $type,
                'per' => $per,
                'min_price' => $min_price,
                'calc' => $calc,
                'details' => $details
            ]);
        } else if ($action == "update") {
            $offer = Offer::find($id);
            $edit_details = $this->editDetails($request, $offer);
            $offer->name = $name;
            $offer->type = $type;
            $offer->per = $per;
            $offer->min_price = $min_price;
            $offer->calc = $calc;
            $offer->details = $details;
            $offer->save();

            if ($edit_details != "") {
                EditReport::create([
                    'user_id' => auth()->user()->id,
                    'edited_type' => 'App\Models\Offer',
                    'edited_id' => $id,
                    'details' => $edit_details
                ]);
            }
        }
        return $offer->showIndex();
    }

    public function delete(Request $request)
    {
        $id = $request->id;
        $offer = Offer::find($id);
        $offer->delete();
        return $offer->showIndex();
    }

    public function export()
    {
        return Export::export(Offer::all(), ['row', 'name', 'type', 'per', 'min_price', 'times_used', 'details']);
    }

    public function crudGame(Request $request)
    {
        $g_id = $request->g_id;
        $offer = new Offer;
        return $offer->crudGame($g_id);
    }

    protected function editDetails(Request $request, Offer $offer)
    {
        $details = "";
        if ($request->name != $offer->name) {
            $details .= "تغییر نام از " . $offer->name . " به " . $request->name . ", \n";
        }
        if ($request->type != $offer->type) {
            $details .= "تغییر نوع از " . $offer->type . " به " . $request->type . ", \n";
        }
        if ($request->per != $offer->per) {
            $details .= "تغییر مقدار از " . cnf($offer->per) . " به " . cnf($request->per) . ", \n";
        }
        if ($request->min_price != $offer->min_price) {
            $details .= "تغییر حداقل مبلغ از " . cnf($offer->min_price) . " به " . cnf($request->min_price) . ", \n";
        }
        $calcs = [
            "game" => "بازی",
            "factor" => "فروشگاه",
            "all" => "کل صورتحساب"
        ];
        if ($request->calc != $offer->calc) {
            $details .= "تغییر نوع محاسبه از " . $calcs[$offer->calc] . " به " . $calcs[$request->calc] . ", \n";
        }
        return $details;
    }

    public function dataTable(Request $request)
    {
        if ($request->ajax()) {
            $data = Offer::query();
            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('per', '{{ cnf($per) }}')
                ->editColumn('min_price', '{{ cnf($min_price) }}')
                ->addColumn('action', function (Offer $offer) {
                    $actionBtn = '';
                    if (Gate::allows('update')) {
                        $actionBtn .= '
                        <button type="button" class="btn btn-warning btn-sm crud" data-action="update" data-id="' . $offer->id . '" data-bs-target="#crud" data-bs-toggle="modal"><i class="bx bx-edit"></i></button>';
                    }
                    if (Gate::allows('delete')) {
                        $actionBtn .= '
                        <button type="button" class="btn btn-danger btn-sm delete-offer" data-id="' . $offer->id . '"><i class="bx bx-trash"></i></button>';
                    }
                    return $actionBtn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
    }
}
