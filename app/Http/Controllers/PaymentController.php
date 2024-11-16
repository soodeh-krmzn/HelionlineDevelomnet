<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Club;
use App\Models\Person;
use App\Models\ClubLog;
use App\Models\Payment;
use App\Services\Export;
use App\Models\EditReport;
use App\Models\PaymentType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Hekmatinasser\Verta\Facades\Verta;
use Yajra\DataTables\Facades\DataTables;

class PaymentController extends Controller
{

    public function index()
    {
        $payment_types = PaymentType::all();
        $payment = new Payment;
        return view('payment.index', compact( 'payment', 'payment_types'));
    }

    public function store(Request $request)
    {
        //dd($request->all());
        $validated = $request->validate([
            'person_id' => 'required|integer|exists:people,id'
        ]);

        $action = $request->action;
        $id = $request->id;
        $person_id = $request->person_id;
        $person = Person::find($person_id);
        $balance = 0;
        parse_str($request->prices, $prices);
        parse_str($request->details, $details);
        $sum = 0;

        if ($action == "create") {
            $pay = Payment::storeAll($prices, $details, $person);
            $balance += $pay['balance'];
            $sum += $pay['clubable'];
        }
        if ($action == "update") {
            $payment = Payment::find($request->id);
            $oldPrice = $payment->price;
            $oldDetials = $payment->details;
            $oldtype=$payment->type;

            $payment->update([
                'price' =>abs($prices['price']) * intval($request->doctype.'1'),
                'details' => $details['details'],
                'type' => $request->paymentTypeSelect ?? $payment->type
            ]);
            $details = '';
            if ($oldPrice != $payment->price) {
                $details .= " تغییر مبلغ از " . price($oldPrice) . ' به ' . price($payment->price) . ",\n";
            }
            if ($oldDetials != $payment->details) {
                $details .= " تغییر توضیحات از " . $oldDetials . ' به ' . $payment->details . "\n";
            }
            if ($oldtype != $payment->type) {
                $details .= " تغییر روش پرداخت از " . $oldtype . ' به ' . $payment->type . "\n";
            }
            if ($details != '') {
                EditReport::create([
                    'user_id' => auth()->user()->id,
                    'edited_type' => 'App\Models\Payment',
                    'edited_id' => $payment->id,
                    'details' => $details
                ]);
            }
        }

        $club = Club::where('type', 'payment')->first();
        if (!is_null($club)) {
            $club->storeClub($person, $sum, "َApp\Models\Payment", 0);
        }

        $person->balance += $balance;
        $person->save();

        return;
    }

    public function crud(Request $request)
    {
        $action = $request->action;
        $id = $request->id;
        $payment = new Payment;

        return $payment->crud($action, $id,$request->debt);
    }

    public function remove(Request $request) {
        // dd($request->all());
        Payment::findOrFail($request->payment_id)->delete();
    }

    public function search(Request $request)
    {
        $person_id = $request->person_id;
        $type = $request->type;
        $from_price = $request->from_price;
        $to_price = $request->to_price;
        if (!is_null($request->from_date)) {
            $from_date = Carbon::parse(dateSetFormat($request->from_date));
        } else {
            $from_date = null;
        }
        if (!is_null($request->to_date)) {
            $to_date = Carbon::parse(dateSetFormat($request->to_date, 1));
        } else {
            $to_date = null;
        }

        $payments = Payment::query();

        if ($person_id != null) {
            $payments->where('person_id', $person_id);
        }
        if ($type != null) {
            $payments->where('type', $type);
        }
        if ($from_price != null) {
            $payments->where('price', '>=', $from_price);
        }
        if ($request->withOffers) {
            $payments->where(function ($query) {
                $query->where('price', '<=', 0)->orWhereIn('type', ['offer','rounded']);
            });
        } else {
            if ($to_price != null) {
                $payments->where('price', '<=', $to_price);
            }
        }
        if ($from_date != null) {
            $payments->where('created_at', '>=', $from_date);
        }
        if ($to_date != null) {
            $payments->where('created_at', '<=', $to_date);
        }

        // $payments = $payments->latest()->orderBy('price', 'desc');

        return $payments;
    }

    public function export(Request $request)
    {
        $data = $this->search($request)->get();
        return Export::export($data, ['person_name', 'price_format', 'type_value', 'details']);
    }

    public function dataTable(Request $request)
    {

        // ini_set('max_execution_time', 300);
        if ($request->has('filter_search')) {
            $data = $this->search($request);
        } else if ($request->all) {
            $data = Payment::query();
        } else {
            $data = Payment::whereDate('created_at', today());
        }
        return DataTables::of($data)
            ->addIndexColumn()
            ->editColumn('created_at', function (Payment $payment) {
                return timeFormat($payment->created_at);
            })
            ->addColumn('person', function (Payment $payment) {
                return $payment->person?->getFullName() ?? '<span class="badge bg-danger">'.__("یافت نشد").'</span>';
            })
            ->editColumn('price', function (Payment $payment) {
                return $payment->getPriceBadge();
            })
            ->editColumn('type', function (Payment $payment) {
                return __($payment->type());
            })
            ->addColumn('action', function (Payment $payment) {
                $actionBtn = '';
                if (Gate::allows('update')) {
                    $actionBtn .= '<button type="button" class="btn btn-warning btn-sm crud" data-action="update" data-id="' . $payment->id . '" data-bs-toggle="modal" data-bs-target="#crud"><i class="bx bx-edit"></i></button>';
                }
                if (Gate::allows('delete')) {
                    $actionBtn .= '<button type="button" class="btn btn-danger btn-sm remove-payment" data-action="delete" data-id="' . $payment->id . '" ><i class="bx bx-trash"></i></button>';
                }
                // if (!in_array($payment->type,['offer','rounded','vat','game','factor']) and Gate::allows('update')) {
                //     $actionBtn .= '<button type="button" class="btn btn-warning btn-sm crud" data-action="update" data-id="' . $payment->id . '" data-bs-toggle="modal" data-bs-target="#crud"><i class="bx bx-edit"></i></button>';
                // }
                // if (!in_array($payment->type,['offer','rounded','vat','game','factor']) and Gate::allows('delete')) {
                //     $actionBtn .= '<button type="button" class="btn btn-danger btn-sm remove-payment" data-action="delete" data-id="' . $payment->id . '" ><i class="bx bx-trash"></i></button>';
                // }

                return $actionBtn;
            })
            ->rawColumns(['action', 'price', 'person'])
            ->make(true);
    }

    public function getSum(Request $request, $action = false)
    {

        if ($request->has('filter_search')) {
            $data = $this->search($request);
        } else if ($request->all) {
            $data = Payment::latest()->get();
        } else if ($action == 'whOffer') {
            $data = Payment::where('price', '>', 0)->whereNot('type', 'offer')->whereDate('created_at', today())->latest()->get();
        } else if ($action == 'offered') {
            $data = Payment::where('type', 'offer')->whereDate('created_at', today())->latest()->get();
        } else if ($action == 'rounded') {
            $data = Payment::where('type', 'rounded')->whereDate('created_at', today())->latest()->get();
        } else if ($action == 'vat') {
            $data = Payment::where('type', 'vat')->whereDate('created_at', today())->latest()->get();
        }else {
            $data = Payment::whereDate('created_at', today())->latest()->get();
        }
        if ($data->count() <= 0) {
            return 0;
        }
        // dump($action.":".$data->count());
        return $data->sum('price');
    }
}
