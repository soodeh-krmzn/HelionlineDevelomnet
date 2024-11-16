<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Game;
use App\Models\Factor;
use App\Models\Person;
use App\Models\Product;
use App\Models\Setting;
use App\Services\Export;
use App\Models\FactorBody;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Hekmatinasser\Verta\Facades\Verta;
use Yajra\DataTables\Facades\DataTables;

class FactorBodyController extends Controller
{
    public function crud(Request $request)
    {
        $f_id = $request->f_id;
        $body = new FactorBody;
        return $body->showIndex($f_id);
    }

    public function store(Request $request)
    {
        $factor_id = $request->f_id;
        $factor_type = $request->f_type;
        $game_id = $request->g_id;
        $product_id = $request->p_id;
        $p = new ProductController;
        $products = $p->search($request);

        DB::beginTransaction();
        try {
            if ($factor_type == "nogame") {
                $person = $this->storePerson($request);
                if ($person->activeGame()) {
                    return response()->json([
                        "message" => "برای این فرد ورود ثبت شده است. لطفا از طریق میزکار اقدام نمایید."
                    ], 400);
                }
                $person_id = $person->id;
            } else {
                $person_id = $request->person_id;
            }

            $factor = Factor::find($factor_id);
            if (!$factor) {
                $factor = $this->storeFactor($game_id, $factor_type, $person_id);
            }

            $product = Product::find($product_id);
            if ($product->stock < 1) {
                return response()->json([
                    "message" => "موجودی این محصول به اتمام رسیده است."
                ], 400);
            }

            $body = FactorBody::updateOrCreate([
                'factor_id' => $factor->id,
                'product_id' => $product_id,
            ], [
                'product_name' => $product->name,
                'product_price' => $product->sale,
                'product_buy_price' => $product->buy,
            ]);

            $body->count += 1;
            $body->body_price = $body->product_price * $body->count;
            $body->body_buy_price = $body->product_buy_price * $body->count;
            $body->save();

            $factor->total_price = $factor->totalPrice();
            $factor->final_price = $factor->total_price - $factor->offer_price;
            $factor->save();
            if ($factor_type == "game") {
                $product->editStock(-1);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'message' => $e->getMessage(),
            ], 500);
        }

        return $factor->crud($factor->game_id, $factor_type, $person_id, $products);
    }

    public function storeFactor($game_id = 0, $factor_type, $person_id = 0)
    {
        if ($factor_type == "game") {
            $game = Game::find($game_id);
            $factor = Factor::create([
                'game_id' => $game_id,
                'person_id' => $game->person_id,
                'person_fullname' => $game->person_fullname,
                'person_mobile' => $game->person_mobile,
            ]);
        } else if ($factor_type == "nogame") {
            $person = Person::find($person_id);
            $factor = Factor::create([
                'person_id' => $person_id,
                'person_fullname' => $person->getFullName(),
                'person_mobile' => $person->mobile,
            ]);
        }
        return $factor;
    }

    public function update(Request $request)
    {
        $id = $request->id;
        $f_type = $request->f_type;
        $count = $request->count;
        $p = new ProductController;
        $products = $p->search($request);
        $body = FactorBody::find($id);
        $product_price = $request->product_price??$body->product_price;
        // $product_price=$body->product_price;
        $body_price = $product_price * $count;
        if (!$body) {
            return response()->json([
                'message' => __('متاسفانه خطایی رخ داده است.')
            ], 404);
        }
        $product = $body->product;
        $stock = $count - $body->count;
        if ($stock > $product->stock) {
            return response()->json([
                'message' => __('تعداد وارد شده از موجودی محصول بیشتر است.')
            ], 400);
        }

        if ($count == 0) {
            $body->delete();
        } else {
            $body->product_price = $product_price;
            $body->count = $count;
            $body->body_price = $body_price;
            $body->body_buy_price = $product->buy * $count;
            $body->save();
        }
        $factor = $body->factor;
        $factor->total_price = $factor->totalPrice();
        $factor->final_price = $factor->total_price - $factor->offer_price;
        $factor->save();

        $person_id = $factor->person_id;

        if($f_type=='game' or $f_type=='load-game')
        $product->editStock($stock * (-1));

        if ($f_type == "load-game" or $f_type=='game') {
            return $factor->showBodies("$f_type");
        } else {
            $f_type='nogame';
            return $factor->crud($factor->game_id, $f_type, $person_id, $products);
        }
    }

    public function delete(Request $request)
    {
        $id = $request->id;
        $f_type = $request->f_type;
        $p = new ProductController;
        $products = $p->search($request);

        $body = FactorBody::find($id);
        if (!$body) {
            return response()->json([
                'message' => 'متاسفانه خطایی رخ داده است.'
            ], 404);
        }

        $product = $body->product;
        $factor = $body->factor;
        $person_id = $factor->person_id;

        $body->delete();

        $factor->total_price = $factor->totalPrice();
        $factor->final_price = $factor->total_price - $factor->offer_price;

        $factor->save();
        if($f_type == "load-game" or $f_type == "game")
        $product->editStock($body->count);

        if ($f_type == "load-game") {
            return $factor->showBodies("load-game");
        } else {
            // $f_type='nogame';
            // dd($factor->game_id, $f_type, $person_id, $products);
            return $factor->crud($factor->game_id, $f_type, $person_id, $products);
        }
    }

    public function storePerson(Request $request)
    {
        $person_id = $request->person_id;
        $setting = new Setting;
        $mobileCount = $setting->mobileCount();
        // dd($request->all());
        $repetitiveMobile = $setting->getSetting('repetitive-mobile');
        $validated = $request->validate([
            'person_name' => 'required',
            'person_family' => 'required',
            'mobile' => ['required', 'digits:' . $mobileCount, $repetitiveMobile != 'true' ?  Rule::unique('people')->ignore($person_id)->where(function ($query) {
                return $query->whereNull('deleted_at');
            }) : ''],
            'person_gender' => 'required',
        ]);

        if ($person_id == 0) {
            $person = Person::create([
                'created_by' => 1,
                'name' => $request->person_name,
                'family' => $request->person_family,
                'gender' => $request->person_gender,
                'mobile' => $request->mobile,
            ]);
        } else {
            $person = Person::find($person_id);
            if (!$person) {
                return response()->json([
                    'message' => 'شخص مورد نظر یافت نشد.'
                ], 404);
            }
            $person->update([
                'name' => $request->person_name,
                'family' => $request->person_family,
                'gender' => $request->person_gender,
                'mobile' => $request->mobile,
            ]);
        }

        return $person;
    }

    public function index()
    {
        Factor::where('closed', 0)->where('game_id',null)->with('bodies')->get()->each(function ($factor) {
            $factor->bodies()->delete(); // Delete related bodies
            $factor->delete(); // Delete the factor itself
        });
        return view('factor-body.index');
    }

    public function search(Request $request)
    {
        $product_id = $request->product_id;

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

        $bodies = FactorBody::query();

        if ($product_id != null) {
            $bodies->where('product_id', $product_id);
        }
        if ($from_date != null) {
            $bodies->where('created_at', '>=', $from_date);
        }
        if ($to_date != null) {
            $bodies->where('created_at', '<=', $to_date);
        }

        // $bodies = $bodies->latest();
        return $bodies;
    }

    public function dataTable(Request $request)
    {
        if ($request->ajax()) {
            if ($request->has('filter_search')) {
                $data = $this->search($request);
            } elseif ($request->all) {
                $data = FactorBody::query();
            } else {
                $data = FactorBody::whereDate('created_at', today());
            }
            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('product_id', function (FactorBody $body) {
                    return $body->product?->name ?? '<span class="badge bg-danger">'.__("یافت نشد").'</span>';
                })
                ->editColumn('created_at', function (FactorBody $body) {
                    return timeFormat($body->created_at);
                })
                ->editColumn('product_price', '{{ cnf($product_price) }}')
                ->editColumn('product_buy_price', '{{ cnf($product_buy_price) }}')
                ->editColumn('body_price', '{{ cnf($body_price) }}')
                ->editColumn('body_buy_price', '{{ cnf($body_buy_price) }}')
                ->rawColumns(['product_id'])
                ->make(true);
        }
    }

    public function getSum(Request $request)
    {
        if ($request->has('filter_search')) {
            $data = $this->search($request);
        } elseif($request->all){
            $data = FactorBody::latest()->get();
        }else {
            $data = FactorBody::whereDate('created_at',today())->get();
        }

        if (!($data->count() > 0)) {
            return 0;
        }
        return ['sum'=>$data->sum('body_price') - $data->sum('body_buy_price'),'total'=>$data->sum('body_price')];
    }

    public function export(Request $request)
    {
        $data = $this->search($request)->get();
        return Export::export($data->map(function ($item) {
            $item['product_price'] = price($item->product_price);
            $item['product_buy_price'] = price($item->product_buy_price);
            $item['body_price'] = price($item->body_price);
            $item['body_buy_price'] = price($item->body_buy_price);
            return $item;
        }), ['product_name', 'product_price', 'product_buy_price', 'count', 'body_price', 'body_buy_price']);
    }
}
