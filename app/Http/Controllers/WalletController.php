<?php

namespace App\Http\Controllers;

use App\Models\Club;
use App\Models\ClubLog;
use App\Models\Payment;
use App\Models\PaymentType;
use App\Models\Person;
use App\Models\Setting;
use App\Models\Wallet;
use App\Services\Export;
use Carbon\Carbon;
use Hekmatinasser\Verta\Facades\Verta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class WalletController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // $wallets = Wallet::all();
        $wallet = new Wallet;
        $setting = new Setting;
        $defaultPaymentType = $setting->getSetting('default_payment_type');
        return view('wallet.index', compact('wallet', 'defaultPaymentType'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'person_id' => 'required|exists:people,id',
            'price' => 'required|numeric'
        ]);

        $action = $request->action;
        $id = $request->id;
        $person = Person::find($request->person_id);
        $price = $request->price;
        $gift = $request->gift;
        $description = $request->description;
        parse_str($request->prices, $prices);
        parse_str($request->details, $details);
        if ($request->gift == "true") {
            $setting = new Setting();
            $gift_percent = $setting->getSetting("wallet_gift_percent") ?? 0;
            $gift_price = ((int)$price * $gift_percent) / 100;
            $final_price = (int)$price + $gift_price;
        } else {
            $final_price = $price;
            $gift_percent = 0;
        }

        $wallet_value = $person->wallet_value + $final_price;
        $b = $person->balance;

        DB::beginTransaction();
        try {
            $wallet = Wallet::create([
                'person_id' => $person->id,
                'balance' => $wallet_value,
                'price' => $price,
                'gift_percent' => $gift_percent,
                'final_price' => $final_price,
                'description' => $description
            ]);

            Payment::create([
                'user_id' => auth()->id(),
                'person_id' => $person->id,
                'price' => $price * (-1),
                'details' => "مبلغ شارژ کیف پول شماره ". $wallet->id,
                'type' => "charge_wallet",
                'object_type' => "App\Models\Wallet",
                'object_id' => $wallet->id
            ]);
            $b -= $price;

            $pay = Payment::storeAll($prices, $details, $person, "App\Models\Wallet", $wallet->id);
            $b += $pay['balance'];

            $person->balance = $b;
            $person->wallet_value = $wallet_value;
            $person->save();

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }

        // return $wallet->showIndex(Wallet::all());
    }

    /**
     * Display the specified resource.
     */
    public function show(Wallet $wallet)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Wallet $wallet)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Wallet $wallet)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Wallet $wallet)
    {
        //
    }

    public function crud(Request $request)
    {
        $action = $request->action;
        $id = $request->id;
        $wallet = new Wallet;

        return $wallet->crud($action, $id);
    }

    public function search(Request $request)
    {
        $person_id = $request->person_id;
        $from_price = $request->from_price;
        $to_price = $request->to_price;
        if (!is_null($request->from_date)) {
            $from_date = Carbon::parse(dateTimeSetFormat($request->from_date));
        } else {
            $from_date = null;
        }
        if (!is_null($request->to_date)) {
            $to_date = Carbon::parse(dateTimeSetFormat($request->to_date));
        } else {
            $to_date = null;
        }

        $wallets = Wallet::query();

        if ($person_id != null) {
            $wallets->where('person_id', $person_id);
        }
        if ($from_price != null) {
            $wallets->where('final_price', '>=', $from_price);
        }
        if ($to_price != null) {
            $wallets->where('final_price', '<=', $to_price);
        }
        if ($from_date != null) {
            $wallets->where('created_at', '>=', $from_date);
        }
        if ($to_date != null) {
            $wallets->where('created_at', '<=', $to_date);
        }

        // $wallets = $wallets->latest();
        return $wallets;
    }

    public function dataTable(Request $request)
    {
        if ($request->ajax()) {
            if ($request->has('filter_search')) {
                $data = $this->search($request);
            } else {
                $data = Wallet::query();
            }
            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('created_at', function(Wallet $wallet) {
                    return timeFormat($wallet->created_at);
                })
                ->editColumn('expire', function(Wallet $wallet) {
                    return dateFormat($wallet->expire) ?? "-";
                })
                ->addColumn('person', function(Wallet $wallet) {
                    return $wallet->person?->getFullName() ?? '<span class="badge bg-danger">'.__("یافت نشد").'</span>';
                })
                ->editColumn('balance', '{{ cnf($balance) }}')
                ->editColumn('price', '{{ cnf($price) }}')
                ->editColumn('gift_percent', '{{ $gift_percent . "%" }}')
                ->editColumn('final_price', '{{ cnf($final_price) }}')
                ->rawColumns(['person'])
                ->make(true);
        }
    }

    public function export(Request $request)
    {
        $data = $this->search($request)->get();
        return Export::export($data);
    }
}
