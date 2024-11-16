<?php

namespace App\Http\Controllers;

use App\Models\PaymentType;
use App\Models\Setting;
use App\Services\Export;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Yajra\DataTables\Facades\DataTables;

class PaymentTypeController extends Controller
{

    public function crud(Request $request, PaymentType $paymentType)
    {
        $paymentType = new PaymentType();
        $action = $request->action;
        if ($action == "create") {
            return $paymentType->crud("create");
        } else if ($action == "update") {
            $id = $request->id;
            return $paymentType->crud("update", $id);
        }
    }

    public function index()
    {
        $paymentType = new PaymentType;
        $setting = new Setting;
        return view('payment-type.index', compact('paymentType', 'setting'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required',
            'label' => 'required',
            'status' => 'required',
            'club' => 'required'
        ]);

        $paymentType = new PaymentType();
        $action = $request->action;
        $id = $request->id;
        $name = str_replace(' ', '_', $request->name);
        $check = PaymentType::where('name', $name)->whereNot('id', $id)->withTrashed()->first();
        if ($check) {
            return response()->json([
                'message' => 'نام نمی تواند تکراری باشد.'
            ], 400);
        }
        $label = $request->label;
        $details = $request->details;
        $status = $request->status;
        $club = $request->club;
        if ($action == "create") {
            PaymentType::create([
                'name' => $name,
                'label' => $label,
                'details' => $details,
                'status' => $status,
                'club' => $club
            ]);
        } else if ($action == "update") {
            $paymentType = PaymentType::find($id);
            $paymentType->name = $name;
            $paymentType->label = $label;
            $paymentType->details = $details;
            $paymentType->status = $status;
            $paymentType->club = $club;
            $paymentType->save();
        }
        return $paymentType->select();
    }

    public function delete(Request $request)
    {
        $id = $request->id;
        $paymentType = PaymentType::find($id);
        $paymentType->delete();
        return $paymentType->select();
    }

    public function export()
    {
        return Export::export(PaymentType::all(),['name','label','details']);
    }

    public function dataTable(Request $request)
    {
        if ($request->ajax()) {
            $data = PaymentType::latest()->get();
            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('status', function(PaymentType $paymentType) {
                    return $paymentType->status();
                })
                ->editColumn('club', function(PaymentType $paymentType) {
                    return $paymentType->club();
                })
                ->addColumn('action', function(PaymentType $paymentType) {
                    $actionBtn = '';
                    if (Gate::allows('update')) {
                        $actionBtn .= '<button type="button" class="btn btn-warning btn-sm crud" data-action="update" data-id="' . $paymentType->id . '" data-bs-target="#crud" data-bs-toggle="modal"><i class="bx bx-edit"></i></button>';
                    }
                    if (Gate::allows('delete')) {
                        $actionBtn .= '
                        <button type="button" class="btn btn-danger btn-sm delete-payment-type" data-id="' . $paymentType->id . '"><i class="bx bx-trash"></i></button>';
                    }
                    return $actionBtn;
                })
                ->rawColumns(['action', 'status', 'club'])
                ->make(true);
        }
    }

}
