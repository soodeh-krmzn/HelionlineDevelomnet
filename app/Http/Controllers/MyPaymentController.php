<?php

namespace App\Http\Controllers;

use App\Models\Admin\Payment;
use App\Models\MyPayment;
use App\Services\Export;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Yajra\DataTables\Facades\DataTables;

class MyPaymentController extends Controller
{
    public function index()
    {
        return view('my-payment.index');
    }

    public function dataTable(Request $request)
    {
        if ($request->ajax()) {
            $data = Payment::where('account_id',auth()->user()->account_id);
            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('created_at', function(Payment $payment) {
                    return timeFormat($payment->created_at);
                })
                ->editColumn('price', '{{ cnf($price) }}')
                ->editColumn('authority', function(Payment $payment) {
                    $authority = str_replace('A', '', $payment->authority);
                    $authority = ltrim($authority, 0);
                    return $authority;
                })
                ->editColumn('status', function(Payment $payment) {
                    return $payment->getStatus();
                })
                ->editColumn('type', function(Payment $payment) {
                    return $payment->getTypeValue();
                })
                ->editColumn('driver', function(Payment $payment) {
                    return __($payment->driver);
                })
                ->rawColumns(['status'])
                ->make(true);
        }
    }

    public function export()
    {
        return Export::export(Payment::where('account_id',auth()->user()->account_id)->latest()->get()->map(function($item) {
            $item->status=$item->getStatusForExcel();
            $item->price=price($item->price);
            $item->paymentType=$item->getTypeValue();
            return $item;
        }),['authority','status','ref_id','message','price','paymentType','driver','username','card']);
    }

    // public function update()
    // {
    //     $payment = MyPayment::latest()->first();
    //     $date = $payment?->created_at;
    //     $account_id = auth()->user()->account_id;

    //     $payments = Payment::where('account_id', $account_id);

    //     if (!is_null($date)) {
    //         $payments->where('created_at', '>', $date);
    //     }

    //     $payments = $payments->get();

    //     foreach ($payments as $payment) {
    //         MyPayment::create([
    //             'authority' => $payment->authority,
    //             'status' => $payment->status,
    //             'ref_id' => $payment->ref_id,
    //             'message' => $payment->message,
    //             'price' => $payment->price,
    //             'type' => $payment->type,
    //             'username' => $payment->username,
    //             'card' => $payment->card,
    //             'pay_created_at' => $payment->created_at
    //         ]);
    //     }

    //     return response()->json([ 'message' => 'ok' ], 200);
    // }
}
