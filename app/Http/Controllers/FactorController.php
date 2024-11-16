<?php

namespace App\Http\Controllers;

use App\Models\Club;
use App\Models\ClubLog;
use App\Models\Factor;
use App\Models\Offer;
use App\Models\Payment;
use App\Models\PaymentType;
use App\Models\Person;
use App\Models\Wallet;
use App\Services\Export;
use Carbon\Carbon;
use Hekmatinasser\Verta\Facades\Verta;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class FactorController extends Controller
{
    public function index()
    {
        $factor = new Factor;
        return view('factor.index', compact('factor'));
    }

    public function crud(Request $request)
    {
        $g_id = $request->g_id;
        $f_type = $request->f_type;
        $factor = new Factor;
        $product = new ProductController;
        $products = $product->search($request);

        return $factor->crud($g_id, $f_type, 0, $products);
    }

    public function products(Request $request)
    {
        $g_id = $request->g_id;
        $f_id = $request->f_id;
        $f_type = $request->f_type;
        $name = $request->name;
        $category = $request->category;

        $factor = new Factor;
        $product = new ProductController;
        $products = $product->search($request);

        return $factor->showProducts($g_id, $f_id, $f_type, $products, $name, $category);
    }

    public function close(Request $request)
    {

        $factor_id = $request->factor_id;
        $person_id = $request->person_id;
        $person = Person::find($person_id);
        parse_str($request->payments, $payments);
        parse_str($request->details, $details);

        $factor = Factor::find($factor_id);

        if (!$factor) {
            return response()->json([
                "message" => __("متاسفانه خطایی رخ داده است.")
            ], 404);
        }
        $factor->update([
            'closed' => 1
        ]);
        $factor->decreaseProducts();
        $offer = Offer::find($factor->offer_code);
        if ($offer) {
            $offer->times_used++;
            $offer->save();
        }

        $total = $factor->total_price * (-1);
        $offer_price = $factor->offer_price;
        $balance = $total + $offer_price;

        Payment::create([
            "user_id" => auth()->id(),
            "person_id" => $person_id,
            'object_type'=>"App\Models\Factor",
            'object_id'=>$factor->id,
            "price" => $total,
            "details" => "مبلغ فاکتور شماره " . $factor->id,
            "type" => "factor"
        ]);

        Payment::create([
            "user_id" => auth()->id(),
            "person_id" => $person_id,
            'object_type'=>"App\Models\Factor",
            'object_id'=>$factor->id,
            "price" => $offer_price,
            "details" => "مبلغ تخفیف فاکتور شماره " . $factor->id,
            "type" => "offer"
        ]);

        $pay = Payment::storeAll($payments, $details, $person, "App\Models\Factor", $factor->id, "پرداخت فاکتور کد " . $factor->id);
        $balance += $pay['balance'];
        $sum = $pay['clubable'];

        $club = Club::where('type', 'factor')->first();
        if (!is_null($club)) {
            $club->storeClub($person, $sum, 'App\Models\Factor', $factor->id);
        }

        $person->balance += $balance;
        $person->save();
    }

    public function offer(Request $request)
    {
        $factor_id = $request->factor_id;
        $offer_type = $request->offer_type;
        $offer_code = $request->offer_code;
        $offer_price = $request->offer_price;
        $p = new ProductController;
        $products = $p->search($request);

        $factor = Factor::find($factor_id);
        if (!$factor) {
            return response()->json([
                "message" => "متاسفانه خطایی رخ داده است."
            ], 404);
        }

        if ($offer_type == 1) {
            $factor->offer_price = $offer_price;
            $factor->offer_code = null;
            $factor->final_price = $factor->total_price - $offer_price;
            $factor->save();
        } else if ($offer_type == 2) {
            $offer = Offer::find($offer_code);
            if (!$offer) {
                return response()->json([
                    "message" => "متاسفانه خطایی رخ داده است."
                ], 404);
            }
            if ($offer->min_price > $factor->total_price) {
                return response()->json([
                    "message" => "این کد برای این فاکتور قابل استفاده نمیباشد."
                ], 400);
            }

            switch ($offer->type) {
                case "درصد":
                    $offer_price = ($factor->total_price * $offer->per) / 100;
                    break;
                case "مبلغ":
                    $offer_price = $offer->per;
                    break;
                default:
                    $offer_price = 0;
            }

            $factor->offer_code = $offer_code;
            $factor->offer_price = $offer_price;
            $factor->final_price = $factor->total_price - $offer_price;
            $factor->save();
            // $offer->times_used++;
            // $offer->save();
        }

        return $factor->crud(0, "nogame", $factor->person_id, $products);
    }

    public static function search(Request $request)
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

        $factors = Factor::query();

        if ($person_id != null) {
            $factors->where('person_id', $person_id);
        }
        if ($type != null) {
            if ($type == "game") {
                $factors->whereNot('game_id', null);
            } else if ($type == "factor") {
                $factors->where('game_id', null);
            }
        }
        if ($from_price != null) {
            $factors->where('final_price', '>=', $from_price);
        }
        if ($to_price != null) {
            $factors->where('final_price', '<=', $to_price);
        }
        if ($from_date != null) {
            $factors->where('created_at', '>=', $from_date);
        }
        if ($to_date != null) {
            $factors->where('created_at', '<=', $to_date);
        }

        $factors = $factors->where('closed', 1);
        return $factors;
    }

    public function export(Request $request)
    {
        $data = $this->search($request)->get();
        return Export::export($data, ['person_fullname', 'total_price_format', 'offer_price_format', 'final_price_format', 'bodies_string']);
    }

    public function dataTable(Request $request)
    {
        if ($request->ajax()) {
            if ($request->has('filter_search')) {
                $data = $this->search($request);
            } elseif ($request->all) {
                $data = Factor::where('closed', 1);
            } else {
                $data = Factor::where('closed', 1)->whereDate('created_at', today());
            }
            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('created_at', function (Factor $factor) {
                    return timeformat($factor->created_at);
                })
                ->editColumn('offer_price', '{{ cnf($offer_price) }}')
                ->editColumn('final_price', '{{ cnf($final_price) }}')
                ->editColumn('game', function (Factor $factor) {
                    return $factor->game ? __("بازی") : __("آزاد");
                })
                ->addColumn('action', function (Factor $factor) {
                    $actionBtn = '<button type="button" class="btn btn-success btn-sm crud-bodies" data-bs-toggle="modal" data-bs-target="#bodies-modal" data-f_id="' . $factor->id . '">'.__("مشاهده جزئیات").'</button>';
                    $actionBtn .= '<button class="btn btn-danger btn-sm delete-factor" data-id="' . $factor->id . '"><i class="bx bx-trash"></i></button>';
                    return $actionBtn;
                })
                ->rawcolumns(['action'])
                ->make(true);
        }
    }

    public function deleteFactor(Request $request)
    {
        // dd($request->all());
        $factor = Factor::findOrFail($request->id);
        Payment::where('object_type', 'App\Models\Factor')->where('object_id', $factor->id)->delete();
        foreach($factor->bodies as $body){
            $body->product()->increment('stock',$body->count);
            $body->delete();
        }
        $factor->delete();
    }

    public function getSum(Request $request)
    {
        if ($request->has('filter_search')) {
            $data = $this->search($request);
        } elseif ($request->all) {
            $data = Factor::where('closed', 1)->get();
        } else {
            $data = Factor::where('closed', 1)->whereDate('created_at', today())->get();
        }

        if ($data->count() <= 0) {
            return 0;
        }

        return $data->sum('final_price');
    }
}
