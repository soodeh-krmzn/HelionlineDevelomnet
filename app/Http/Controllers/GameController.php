<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Club;
use App\Models\Game;
use App\Models\Vote;
use App\Models\Group;
use App\Models\Offer;
use App\Services\SMS;
use App\Models\Person;
use App\Models\SmsLog;
use App\Models\Wallet;
use App\Models\ClubLog;
use App\Models\Counter;
use App\Models\Payment;
use App\Models\Section;
use App\Models\Setting;
use App\Models\Station;
use App\Models\GameMeta;
use App\Services\Export;
use App\Models\Adjective;
use App\Models\EditReport;
use App\Models\PersonMeta;
use App\Models\CounterItem;
use App\Models\PaymentType;
use Illuminate\Http\Request;
use Hekmatinasser\Verta\Verta;
use Illuminate\Validation\Rule;
use App\Models\Admin\SmsPattern;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Yajra\DataTables\Facades\DataTables;

class GameController extends Controller
{

    public function index()
    {
        $sections = Section::all();
        $game = new Game;
        return view('game.index', compact('game', 'sections'));
    }

    public function sumPerSectionView($data)
    {
        $request = new Request;
        $request->merge($data);
        $result = $this->searchReport($request);
        $data = $result->select(
            'section_name',
            DB::raw('SUM(final_price) as final_price'),
            DB::raw(
                'SUM(game_price) as game_price'
            ),
            DB::raw(
                'SUM(offer_price) as offer_price'
            ),
            DB::raw(
                'SUM(vat_price) as vat_price'
            ),
            DB::raw(
                'SUM(total_shop) as total_shop'
            ),
            DB::raw(
                'count(*) as count_games'
            ),
            DB::raw(
                'SUM(count) as sum_count'
            ),
            DB::raw(
                'SUM(final_price_after_round)-Sum(final_price_before_round) as round_price'
            )
        )
            ->groupBy('section_name')->get();
        $factors = FactorController::search($request);
        $factors = $factors->selectRaw("SUM(total_price) as total_price, SUM(offer_price) as offer_price, SUM(final_price) as final_price")
            ->where('game_id', null)
            ->first();

        $charges = $this->chargePackage($request);
        $courses = $this->coursePayments($request);
        return view('report.sections.showPerSection', compact('data', 'factors', 'charges', 'courses'));
    }

    public function chargePackage(Request $request)
    {
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

        $ch = Payment::where('type', 'charge_package');
        if ($from_date)
            $ch->where('created_at', '>', $from_date);
        if ($to_date)
            $ch->where('created_at', '<', $to_date);
        return $ch->sum('price');
    }
    public function coursePayments(Request $request)
    {
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

        $ch = Payment::where('type', 'course');
        if ($from_date)
            $ch->where('created_at', '>', $from_date);
        if ($to_date)
            $ch->where('created_at', '<', $to_date);
        return $ch->sum('price');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'section_id' => 'required',
            'count' => 'required',
            'rate' => 'required',
            'deposit_type' => 'required_with:deposit'
        ]);

        DB::beginTransaction();
        try {
            $person = $this->storePerson($request);
            if ($person->activeGame()) {
                return response()->json([
                    'message' => __('ورود تکراری!')
                ], 400);
            }
            if ($request->inDateTime) {
                $date = dateSetFormat($request->inDateTime, 0, 1);
            } else {
                $date = now();
            }
            if ($date->gt(now())) {
                return response()->json([
                    'message' => __('زمان ورود نمی تواند بیشتر از زمان حال باشد!')
                ], 400);
            }
            if ($person->sharj_type == 'times' && $person->isNotExpired() && $request->count > $person->sharj) {
                return response()->json([
                    'message' => __("تعداد افراد وارد شده بیش از مرتبه شارژ موجود است.") . PHP_EOL . __("messages.chargeTimes", ['times' => $person->sharj])
                ], 400);
            }
            $section = Section::find($request->section_id);
            $station = Station::find($request->station_id);
            $counter = Counter::find($request->counter_id);
            $in_time = $date->format("H:i:s");
            $in_date = $date->format('Y-m-d');
            $setting = new Setting();
            $offerCode = $setting->getSetting('global_offer');
            $game = Game::create([
                'user_id' => auth()->id(),
                'offer_code' => $offerCode,
                'person_id' => $person->id,
                'person_fullname' => $request->person_name . ' ' . $request->person_family,
                'person_mobile' => $request->person_mobile,
                'section_id' => $request->section_id,
                'section_name' => $section?->name ?? null,
                'station_id' => $request->station_id,
                'station_name' => $station?->name ?? null,
                'counter_id' => $request->counter_id,
                'counter_name' => $counter?->name ?? null,
                'counter_min' => $counter?->min ?? null,
                'count' => $request->count,
                'seperated' => $request->seperate ? 1 : 0,
                'deposit' => $request->deposit ?? 0,
                'deposit_type' => $request->deposit_type,
                'adjective' => json_encode($request->adjectives),
                'accompany_name' => $request->accompany_name,
                'accompany_mobile' => $request->accompany_mobile,
                'accompany_relation' => $request->accompany_relation,
                'in_time' => $in_time,
                'in_date' => $in_date,
                'group_id' => $request->group_id ?? null
            ]);

            if ($counter) {
                CounterItem::create([
                    'g_id' => $game->id,
                    'title' => $game->person_fullname,
                    'start_date' => $date,
                    'min_duration' => $counter->min,
                ]);
            }

            if ($request->seperate == "true") {

                for ($i = 0; $i < $request->count; $i++) {

                    $t = GameMeta::create([
                        'g_id' => $game->id,
                        'key' => $request->rate,
                        'value' => 1,
                        'u_id' => $i + 1,
                        'start' =>  $date,
                    ]);
                    // dd($i+1,$t);
                }
            } else {
                GameMeta::create([
                    'g_id' => $game->id,
                    'key' => $request->rate,
                    'value' => $request->count,
                    'start' =>  $date,
                ]);
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'message' => $e->getMessage(),
            ], 500);
        }
        $setting = new Setting;
        try {
            $sms = new SMS();
            $sms->send_sms('ورود مشتری', $person->mobile, [
                'name' => $person->name,
                'login' =>  timeFormat($game->in_time, 1)->format('H:i'),
                'camera_link' => $setting->getSetting('camera_link')
            ]);
        } catch (\Exception $e) {
            //
        }
        $result = $this->crowdTime();
        if (isset($result['error'])) {
            return $result['error'];
        }
        return $game->showIndex();
    }

    public function crud()
    {
        $game = new Game;
        $section = new Section;
        $station = new Station;
        $adjective = new Adjective;
        $counter = new Counter;
        return $game->crud($section, $station, $adjective, $counter);
    }

    public function searchPerson(Request $request)
    {
        $input = $request->input;

        if ($input != "") {
            $persons = Person::query()
                ->where(DB::raw("CONCAT(name, ' ', family)"), "like", "%" . $input . "%")
                ->orWhere("mobile", "like", "%" . $input . "%")
                ->orWhere("card_code", "like", "%" . $input . "%")
                ->orWhere("reg_code", "like", "%" . $input . "%")
                ->get();
        } else {
            $persons = "";
        }

        return response()->json([
            'persons' => $persons
        ]);
    }

    public function loadGame(Request $request)
    {
        $game = new Game;
        if ($request->details) {
            return $game->loadGameDetails($request->id);
        }
        return $game->loadGame($request->id);
    }

    public function storePerson(Request $request)
    {
        $person_id = $request->person_id;
        $setting = new Setting;
        $mobileCount = $setting->mobileCount();
        $repetitiveMobile = $setting->getSetting('repetitive-mobile');
        // dd($request->person_id,$request->person_id==0,$request->person_id===0);
        $validated = $request->validate([
            'person_name' => 'required',
            'person_family' => 'required',
            'person_mobile' => ['required', 'digits:' . $mobileCount, $repetitiveMobile != 'true' ?  Rule::unique('people', 'mobile')->ignore($person_id)->where(function ($query) {
                return $query->whereNull('deleted_at');
            }) : ''],
            'person_gender' => 'required',
            'person_national_code' => ['nullable', Rule::unique('people', 'national_code')->ignore($person_id)->where(function ($query) {
                return $query->whereNull('deleted_at'); // Exclude soft-deleted records
            })]
        ]);

        if ($request->person_birth != "") {
            $birth = Carbon::parse(dateSetFormat($request->person_birth));
        } else {
            $birth = null;
        }

        if ($person_id == 0) {
            $person = Person::create([
                'created_by' => auth()->id(),
                'name' => $request->person_name,
                'family' => $request->person_family,
                'birth' => $birth,
                'shamsi_birth' => $request->person_birth,
                'address' => $request->person_address,
                'gender' => $request->person_gender,
                'mobile' => $request->person_mobile,
                'national_code' => $request->person_national_code,
                'reg_code' => $request->person_reg_code,
                'club' => 1
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
                'birth' => $birth,
                'address' => $request->person_address,
                'gender' => $request->person_gender,
                'mobile' => $request->person_mobile,
                'national_code' => $request->person_national_code,
                'reg_code' => $request->person_reg_code
            ]);
        }
        if (!is_null($request->person_address)) {
            PersonMeta::updateOrCreate([
                'p_id' => $person->id,
                'meta' => 'address'
            ], [
                'value' => $request->person_address
            ]);
        }

        return $person;
    }

    public function storeOffer(Request $request)
    {
        $validated = $request->validate([
            'g_id' => 'required',
            'offer_type' => 'required',
            'offer_code' => 'required_if:offer_type,2|nullable|integer',
            'offer_price' => 'required_if:offer_type,1|nullable|numeric'
        ]);

        $g_id = $request->g_id;
        $offer_code = $request->offer_code;
        $offer_price = $request->offer_price;
        $offer_calc = $request->offer_calc;

        $game = Game::find($g_id);
        if (!$game) {
            return response()->json([
                'message' => 'متاسفانه خطایی رخ داده است.'
            ], 404);
        }

        $offer = Offer::find($offer_code);
        $game->offer_code = $offer_code ?? 0;
        $game->offer_price = $offer_price ?? 0;
        $game->offer_calc = $offer->calc ?? $offer_calc ?? "all";
        $game->offer_name = "disable";

        $game->save();
        // if ($offer) {
        //     $offer->times_used++;
        //     $offer->save();
        // }
        return;
    }

    public function close(Request $request)
    {

        // dump($request->all());
        $game_id = $request->g_id;
        $using_sharj = $request->using_sharj ?? true;
        if (!is_array($request->payments)) {
            parse_str($request->payments, $payments);
            parse_str($request->details, $details);
        } else {
            $payments = $request->payments;
            $details = $request->details;
        }

        $game = game::find($game_id);
        // dd($game->counter);
        $person = $game->person;
        if (!$person) {
            return response()->json([
                'message' => 'شخص یافت نشد.'
            ], 404);
        }

        DB::beginTransaction();
        // try {
        $metas = $game->metas->where("end", null);
        foreach ($metas as $meta) {
            $meta->end = now();
            $meta->save();
        }

        $out_time = now()->format("H:i:s");
        $out_date = today();

        $prices = $game->calcPrice($using_sharj);
        // dd($using_sharj, $prices );
        $offer = Offer::find($game->offer_code);
        // $offer_price = $game->offer_price;
        // $offer_name = $game->offer_name == 'disable' ? null : $game->offer_name;
        if ($offer) {
            $offer->times_used++;
            $offer->save();
        }

        $game->update([
            "status" => 1,
        ]);
        if ($game->counter) {
            CounterItem::where('g_id', $game->id)->update([
                'end' => 1
            ]);
        }
        Payment::create([
            "user_id" => auth()->id(),
            "person_id" => $game->person_id,
            "price" => $game->game_price * (-1),
            "details" => "مبلغ بازی کد " . $game->id,
            "type" => "game",
            "object_type" => "App\Models\Game",
            "object_id" => $game->id
        ]);
        $balance = $game->game_price * (-1);
        if ($game->final_price_after_round != $game->final_price_before_round) {
            $round = $game->final_price_after_round - $game->final_price_before_round;
            Payment::create([
                "user_id" => auth()->id(),
                "person_id" => $game->person_id,
                "price" =>  $round * (-1),
                "details" => "مبلغ رند شده  بازی کد " . $game->id,
                "type" => "rounded",
                "object_type" => "App\Models\Game",
                "object_id" => $game->id
            ]);
            $balance += $round * (-1);
        }
        if ($game->vat_price > 0) {
            Payment::create([
                "user_id" => auth()->id(),
                "person_id" => $game->person_id,
                "price" => $game->vat_price * (-1),
                "details" => "مبلغ مالیات  بازی کد " . $game->id,
                "type" => "vat",
                "object_type" => "App\Models\Game",
                "object_id" => $game->id
            ]);
            $balance -= $game->vat_price;
        }
        if ($game->deposit > 0) {
            Payment::create([
                "user_id" => auth()->id(),
                "person_id" => $game->person_id,
                "price" => $game->deposit,
                "details" => "مبلغ پیش پرداخت  بازی کد " . $game->id,
                "type" => $game->deposit_type,
                "object_type" => "App\Models\Game",
                "object_id" => $game->id
            ]);
            $balance += $game->deposit;
        }
        if ($game->factor) {
            Payment::create([
                "user_id" => auth()->id(),
                "person_id" => $game->person_id,
                "price" => $prices["factor_price"] * (-1),
                "details" => "مبلغ فاکتور  بازی کد " . $game->id,
                "type" => "factor",
                "object_type" => "App\Models\Factor",
                "object_id" => $game->factor?->id
            ]);
            $game->factor->update([
                'closed' => 1
            ]);
            $balance -= $prices["factor_price"];
        }

        if ($game->offer_price > 0) {
            Payment::create([
                "user_id" => auth()->id(),
                "person_id" => $game->person_id,
                "price" => $game->offer_price,
                "details" => "مبلغ تخفیف بازی کد " . $game->id,
                "type" => "offer",
                "object_type" => "App\Models\Game",
                "object_id" => $game->id
            ]);
            $balance += $game->offer_price;
        }

        $pay = Payment::storeAll($payments, $details ?? [], $person, "App\Models\Game", $game->id, "پرداخت بازی کد " . $game->id);
        $balance += $pay['balance'];
        $sum = $pay['clubable'];
        $calculateClub = 0;
        if ($sum != 0) {
            $calculateClub = $this->calculateClub($sum, $game->game_price, $prices["factor_price"], $person, $game);
        }
        // dump($balance);
        $person->balance += $balance;
        if ($using_sharj) {
            $person->sharj -= $game->used_sharj;
        }
        $person->save();

        DB::commit();

        if ($request->no_logout_message == "false") {
            $sms = new SMS();
            $sms->send_sms('خروج مشتری', $person->mobile, [
                'name' => $person->name,
                'login' => timeFormat($game->in_time, 1)->format('H:i'),
                'logout' => timeFormat($game->out_time, 1)->format('H:i'),
                'shop' => $prices['factor_price'],
                'price' => $game->final_price
            ]);
        }

        if ($request->no_club_message == "false" && $calculateClub != 0) {
            $sms = new SMS();
            $sms->send_sms('باشگاه مشتریان', $person->mobile, [
                'name' => $person->name,
                'price' => cnf($calculateClub['sum']),
                'wallet_value' => cnf($person->wallet_value)
            ]);
        }
        if ($request->no_feedback_message == "false") {
            $vote = Vote::where('status', 1)->first();

            if (!is_null($vote)) {
                $sms = new SMS();
                $sms->send_sms('نظرسنجی', $person->mobile, [
                    'account' => auth()->user()->account_id
                ]);
            }
        }
        // } catch (\Exception $e) {
        //
        // }

        return $game->showIndex();
    }

    public function crowdTime()
    {
        $setting = new setting;
        $paternId = $setting->getSetting('هشدار شلوغی');
        if ($paternId) {
            $game = new Game;
            if (!($crowd_number = $setting->getSetting('crowd_number'))) {
                return ['error' => response()->json([
                    'message' => __('شما پیامک شلوغی را فعال کرده اید، لطفا تعداد افراد مورد نظر برای حالت شلوغی را در پیکربندی عمومی وارد کنید')
                ], 404)];
            }
            $attendees = $game->where('status', 0)->sum('count');
            $crowdSms = $setting->getSetting('crowd_sms');
            $account = auth()->user()->account;
            if (!$crowdSms and $attendees >= $crowd_number) {
                $sms = new SMS();
                $sms->send_sms('هشدار شلوغی', $account->mobile, [
                    'number' => $attendees
                ]);
                $setting->updateOrCreate([
                    'meta_key' => 'crowd_sms'
                ], [
                    'meta_value' => currentDateTime()
                ]);
            } else if ($crowdSms and $attendees < $crowd_number) {
                $setting->updateOrCreate([
                    'meta_key' => 'crowd_sms'
                ], [
                    'meta_value' => ''
                ]);
            }
        }
    }

    public function printGame($g_id)
    {
        $game = Game::find($g_id);
        $setting = new Setting;
        $title = $setting->getSetting('titleSize');
        $text = $setting->getSetting('textSize');
        $head = $setting->getSetting('headSize');
        $curr = $setting->getSetting('currSize');
        $sizes = [
            'title' => $title ? $title . 'px !important' : 'initial',
            'head' => $head ? $head . 'px !important' : 'initial',
            'text' => $text ? $text . 'px !important' : 'initial',
            'curr' => $curr ? $curr . 'px !important' : 'initial',
        ];
        $bill = [
            'head' => $setting->getSetting('bill_header'),
            'sign' => $setting->getSetting('bill_sign'),
        ];
        return view('game.print', compact('game', 'sizes', 'bill'));
    }


    protected function calculateClub($sum, $game_price, $factor_price, $person, $game)
    {
        if ($sum > $game_price + $factor_price) {
            $sum = $game_price + $factor_price;
        }

        $g = 0;
        $f = 0;
        $game_club = Club::where('type', 'game')->first();
        if (!is_null($game_club)) {
            $g_price = ($game_price / ($game_price + $factor_price)) * $sum;
            $g = $game_club->storeClub($person, $g_price, 'App\Models\Game', $game->id);
        }

        $factor_club = Club::where('type', 'factor')->first();
        if (!is_null($factor_club)) {
            $f_price = ($factor_price / ($game_price + $factor_price)) * $sum;
            $f = $factor_club->storeClub($person, $f_price, 'App\Models\Factor', $game->factor?->id);
        }
        $s = $g + $f;
        return ['game' => $g, 'factor' => $f, 'sum' => $s];
    }

    public function searchReport(Request $request)
    {
        $person_id = $request->person_id;
        $section_id = $request->section_id;
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

        if ($request->trashed == "true") {
            $games = Game::onlyTrashed();
        } else {
            $games = Game::where('status', 1);
        }

        if ($person_id != null) {
            $games->where('person_id', $person_id);
        }
        if ($section_id != null) {
            $games->where('section_id', $section_id);
        }
        if ($from_date != null) {
            $games->where('out_date', '>=', $from_date);
        }
        if ($to_date != null) {
            $games->where('out_date', '<=', $to_date);
        }

        // $games = $games->latest();
        return $games;
    }

    public function export(Request $request)
    {
        $data = $this->searchReport($request)->get();
        return Export::export($data->map(function ($item) {
            $item['person_name'] = $item->person_fullname;
            $item['in_date'] = dateFormat($item->in_date);
            $item['out_date'] = dateFormat($item->out_date);
            $item['common_price'] = price($item->total_price);
            $item['extra_price'] = price($item->extra_price);
            $item['game_price'] = price($item->game_price);
            $item['total_vip_price'] = price($item->total_vip_price);
            $item['offer_price'] = price($item->offer_price);
            $item['final_price'] = price($item->final_price);
            $item['prepayment'] = price($item->deposit);
            return $item;
        }), [
            'person_name',
            'person_mobile',
            'count',
            'in_time',
            'out_time',
            'in_date',
            'out_date',
            'common_price',
            'total_vip_price',
            'extra_price',
            'game_price',
            'offer_price',
            'final_price',
            'section_name',
            'station_name',
            'counter_name',
            'counter_min',
            'counter_passed',
            'prepayment',
            'deposit_type',
            'accompany_name',
            'accompany_mobile',
            'accompany_relation'
        ]);
    }

    public function dataTable(Request $request)
    {
        if ($request->ajax()) {
            if ($request->has('filter_search')) {
                $data = $this->searchReport($request);
            } elseif ($request->all == true) {
                // dd($request->all());
                $data = Game::where('status', 1);
            } else {
                $data = Game::where('status', 1)->whereDate('out_date', today());
            }
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('person', function (Game $game) {
                    return $game->person?->getFullName() ?? '<span class="badge bg-danger">' . __("یافت نشد") . '</span>';
                })
                ->editColumn('section_name', function (Game $game) {
                    return $game->section?->name;
                })
                ->editColumn('in_time', function (Game $game) {
                    return $game->in_time . ' - ' . dateFormat($game->in_date);
                })
                ->editColumn('out_time', function (Game $game) {
                    return $game->out_time . ' - ' . ($game->out_date ? dateFormat($game->out_date) : "");
                })
                ->editColumn('total_shop', function (Game $game) {
                    return cnf($game->total_shop ?? $game->calcPrice()["factor_price"]);
                })
                ->editColumn('game_price', function (Game $game) {
                    return cnf($game->game_price ?? $game->calcPrice()["game_price"]);
                })
                ->editColumn('offer_price', function (Game $game) {
                    return cnf($game->offer_price ?? $game->calcPrice()["offer_price"]) . ($game->offer_name ? " <small>(" . $game->offer_name . ")</small>" : '');
                })
                ->editColumn('final_price', function (Game $game) {
                    return cnf($game->final_price ?? $game->calcPrice()["a_offer_price"]);
                })
                ->addColumn('action', function (Game $game) {
                    $actionBtn = '';
                    if (Gate::allows('update')) {
                        $actionBtn .= '<button type="button" class="btn btn-warning btn-sm crud" data-action="update" data-id="' . $game->id . '" data-bs-target="#crud" data-bs-toggle="modal"><i class="bx bx-edit"></i></button>';
                    }
                    if (Gate::allows('delete')) {
                        $actionBtn .= '
                        <button type="button" class="btn btn-danger btn-sm delete-game" data-id="' . $game->id . '"><i class="bx bx-trash"></i></button>';
                    }
                    $actionBtn .= '
                                 <button type="button" class="btn btn-success btn-sm crud-meta" data-bs-toggle="modal" data-bs-target="#meta-modal" data-g_id="' . $game->id . '"><i class="bx bx-list-ul"></i></button>';
                    if ($game->status and Carbon::create($game->in_date)->gte(Carbon::create('2024-6-18'))) {
                        $actionBtn .= '
                                 <button type="button" id="print-bill" class="btn btn-info btn-sm game-details" data-id="' . $game->id . '"><i class="bx bx-printer"></i></button>';
                        // $actionBtn .= '
                        //          <button type="button" class="btn btn-info btn-sm game-details" data-bs-toggle="modal" data-bs-target="#game-modal" data-g_id="' . $game->id . '"><i class="bx bx-printer"></i></button>';
                    }
                    $accompany = '';
                    if (!is_null($game->accompany_name) || !is_null($game->accompany_mobile)) {
                        $accompany = '
                        <button type="button" class="btn btn-primary btn-sm accompany-modal" data-bs-toggle="modal" data-bs-target="#accompany-modal" data-g_id="' . $game->id . '">اطلاعات همراه</button>';
                    }
                    return $actionBtn . $accompany;
                })
                ->rawColumns(['action', 'person', 'offer_price'])
                ->make(true);
        }
    }
    public function edit(Request $request)
    {
        $id = $request->id;
        $game = Game::find($id);
        if (!$game) {
            return response()->json([
                'message' => 'یافت نشد.'
            ], 404);
        }

        return $game->edit();
    }

    public function update(Request $request)
    {

        $id = $request->id;
        $game = Game::find($id);
        if (!$game) {
            return response()->json([
                'message' => 'یافت نشد.'
            ], 404);
        }

        $in = dateTimeSetFormat($request->in);
        $out = dateTimeSetFormat($request->out);
        if ($in > $out) {
            return response()->json([
                'message' => 'زمان نامعتبر!'
            ], 400);
        }
        $in_time = Carbon::parse($in)->format("H:i:s");
        $in_date = Carbon::parse($in)->format("Y-m-d");
        $out_time = Carbon::parse($out)->format("H:i:s");
        $out_date = Carbon::parse($out)->format("Y-m-d");


        $details = "تغییر زمان ورود از " . $game->in_time . " به " . $in_time . " , \n" .
            "تغییر تاریخ ورود از " .  DateFormat($game->in_date) . " به " . DateFormat($in_date) . " , \n" .
            "تغییر زمان خروج از " . $game->out_time . " به " . $out_time . " , \n" .
            "تغییر تاریخ خروج از " . DateFormat($game->out_date) . " به " . DateFormat($out_date) . " , \n" .
            "تغییر مبلغ تخفیف از " . $game->offer_price . " به " . $request->offer_price . " , \n" .
            "تغییر مبلغ بازی از " . $game->game_price . " به " . $request->game_price . " , \n" .
            "تغییر مبلغ فروشگاه از " . $game->total_shop . " به " . $request->factor_price . " , \n" .
            "تغییر کد بخش از " . $game->section_id . " به " . $request->section_id . " , \n";

        $game->in_time = $in_time;
        $game->in_date = $in_date;
        $game->out_time = $out_time;
        $game->out_date = $out_date;
        $game->offer_price = $request->offer_price;
        $game->game_price = $request->game_price;
        $game->total_shop = $request->factor_price;
        $game->final_price = $request->factor_price + $request->game_price - $request->offer_price;
        $game->section_id = $request->section_id;
        $section = Section::find($request->section_id);
        if (!is_null($section)) {
            $game->section_name = $section->name;
        }
        $game->updated_by = auth()->id();
        $game->save();

        EditReport::create([
            'user_id' => auth()->id(),
            'edited_type' => 'App\Models\Game',
            'edited_id' => $game->id,
            'details' => $details
        ]);

        // $games = Game::all();

        // return $game->showReports($games);
    }

    public function delete(Request $request)
    {
        $id = $request->id;
        $game = Game::find($id);
        if (!$game) {
            return response()->json([
                'message' => "یافت نشد."
            ], 404);
        }
        Payment::where('object_type', 'App\Models\Game')->where('object_id', $game->id)->delete();
        $game->delete();

        // $games = Game::all();
        // return $game->showReports($games);
    }

    public function showAccompany(Request $request)
    {
        $game = Game::find($request->g_id);
        if (!$game) {
            return response()->json([
                'message' => 'یافت نشد.'
            ], 404);
        }

        return $game->showAccompany();
    }

    public function getSum(Request $request, $final_price = true)
    {
        if ($request->has('filter_search')) {
            $data = $this->searchReport($request)->get();
        } elseif ($request->all == true) {
            $data = Game::where('status', 1)->latest()->get();
        } else {
            $data = Game::where('status', 1)->whereDate('out_date', today())->latest()->get();
        }
        if ($final_price == false) {
            return $data->sum('game_price');
        }
        if ($data->count() <= 0) {
            return [
                'sumPrice' => 0,
                'sumHours' => 0
            ];
        }
        $min = 0;
        $sumNormal = 0;
        $sumVip = 0;
        $sumExtra = 0;
        foreach ($data as $row) {

            $min += ($row->total + $row->total_vip + $row->extra);
            $sumNormal += $row->total;
            $sumVip += $row->total_vip;
            $sumExtra += $row->extra;
        }
        return [
            'sumPrice' => $data->sum('final_price'),
            'sumHours' => formatDurationStr($min),
            'sumExtra'=>formatDurationStr($sumExtra),
            'sumVip'=>formatDurationStr($sumVip),
            'sumNormal'=>formatDurationStr($sumNormal),

        ];
    }

    public function indexTable(Request $request)
    {
        $section_id = $request->section_id;
        if ($request->ajax()) {
            if ($section_id > 0) {
                $data = Game::where("status", 0)->where("section_id", $section_id)->latest()->get();
            } else {
                $data = Game::where("status", 0)->latest()->get();
            }
            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('person_fullname', function (Game $game) {
                    return $game->person_fullname . '<br><span class="text-danger">' . $game->group?->name . '</span>';
                })
                ->addColumn('reg_code', function (Game $game) {
                    return $game->person?->reg_code ?? '-';
                })
                ->editColumn('counter_id', function (Game $game) {
                    return $game->getCounter();
                })
                ->editColumn('group_name', function (Game $game) {

                    //  $res=$game->person->groupNames();
                    $res = $game->person->groupNames();
                    return $res ? $res : '-';
                })
                ->editColumn('station_name', function (Game $game) {
                    return $game->station_name ?? '<span class="badge bg-danger">' . __('هیچکدام') . '</span>';
                })
                ->addColumn('action', function (Game $game) {
                    $setting = new Setting;
                    $actionBtn = '';
                    if ($setting->getSetting('load_game_type') == 'modal' || $setting->getSetting('load_game_type') == '') {
                        $actionBtn .= '
                            <button data-bs-toggle="modal" data-bs-target="#game-modal" data-id="' . $game->id . '" class="load-game btn btn-sm btn-info">
                                <i class="bx bx-check" data-bs-toggle="tooltip" data-bs-offset="0,8" data-bs-placement="top" data-bs-custom-class="tooltip-info" title="' . __('استعلام') . '"></i>
                            </button>';
                    }
                    if ($setting->getSetting('load_game_type') == 'page') {
                        $actionBtn .= '
                            <button data-id="' . $game->id . '" class="load-game btn btn-sm btn-info">
                                <i class="bx bx-check" data-bs-toggle="tooltip" data-bs-offset="0,8" data-bs-placement="top" data-bs-custom-class="tooltip-info" title="' . __('استعلام') . '"></i>
                            </button>';
                    }
                    $actionBtn .= '
                                  <button data-bs-toggle="modal" data-bs-target="#person-info-modal" class="btn btn-sm btn-primary crud-person-info" data-p_id="' . $game->person_id . '">
                                      <i class="bx bx-question-mark"data-bs-toggle="tooltip" data-bs-offset="0,8" data-bs-placement="top" data-bs-custom-class="tooltip-primary" title="' . __('توضیحات') . '"></i>
                                  </button>
                                  <button data-bs-toggle="modal" data-bs-target="#factor-modal" class="btn btn-sm btn-warning crud-factor" data-g_id="' . $game->id . '" data-f_type="game">
                                      <i class="bx bx-coffee" data-bs-toggle="tooltip" data-bs-offset="0,8" data-bs-placement="top" data-bs-custom-class="tooltip-warning" title="' . __('فروشگاه') . '"></i>
                                  </button>
                                  <button data-bs-toggle="modal" data-bs-target="#changes-modal" class="btn btn-sm btn-danger crud-changes" data-g_id="' . $game->id . '">
                                      <i class="bx bx-plus"data-bs-toggle="tooltip" data-bs-offset="0,8" data-bs-placement="top" data-bs-custom-class="tooltip-danger" title="' . __('تغییرات') . '"></i>
                                  </button>
                                  <button data-bs-toggle="modal"  data-id=' . $game->id . ' data-bs-target="#board" class="btn btn-sm btn-primary edit-counter" data-g_id="<?php echo $game->id ?>">
                                    <i class="bx bx-time" data-bs-toggle="tooltip" data-bs-offset="0,8" data-bs-placement="top" data-bs-custom-class="tooltip-primary" title="' . __('شمارنده') . '"></i>
                                </button>
                                  <button data-bs-toggle="modal" data-bs-target="#offer_modal1334" class="btn btn-sm btn-dark crud-offer" data-g_id="' . $game->id . '">
                                      <i class="bx bx-minus"data-bs-toggle="tooltip" data-bs-offset="0,8" data-bs-placement="top" data-bs-custom-class="tooltip-dark" title="' . __('تخفیف') . '"></i>
                                  </button>';
                    if (!is_null($game->accompany_name) || !is_null($game->accompany_mobile)) {
                        $actionBtn .= '
                                        <button data-bs-toggle="modal" data-bs-target="#accompany-modal" class="btn btn-sm btn-success accompany-modal" data-g_id="' . $game->id . '">
                                            <i class="bx bx-user" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-custom-class="tooltip-success" title="' . __('همراه') . '"></i>
                                        </button>';
                    }
                    return $actionBtn;
                })
                ->addColumn('person_meta', function (Game $game) {
                    return $game->person?->getMeta("person-info");
                })
                ->addColumn('checkbox', function (Game $game) {
                    return '<input type="checkbox" name="games[]" data-group-id="' . $game->group_id . '" value="' . $game->id . '" class="form-check-input select-game">';
                })
                ->rawColumns(['action', 'checkbox', 'counter_id', 'station_name', 'person_fullname'])
                ->make(true);
        }
    }

    public function groupStore(Request $request)
    {
        $section = Section::find($request->section_id);
        if (!$section) {
            return response()->json([
                'message' => 'یافت نشد.'
            ], 404);
        }
        parse_str($request->people, $people);
        $errors = [];
        foreach ($people['people'] as $id) {
            try {
                $person = Person::find($id);
                $request->merge([
                    'section_id' => $request->section_id,
                    'count' => 1,
                    'rate' => 'normal',
                    'group_id' => $request->group_id,
                    'person_id' => $person->id,
                    'person_name' => $person->name,
                    'person_family' => $person->family,
                    'person_mobile' => $person->mobile,
                    'person_gender' => $person->gender
                ]);
                $this->store($request);
            } catch (\Exception $e) {
                array_push($errors, $e->getMessage());
            }
        }

        return $errors;
    }

    public function getGroups()
    {
        $group_ids = Game::where('status', 0)->groupBy('group_id')->pluck('group_id')->unique();
        $groups = Group::whereIn('id', $group_ids)->get();
        $group = new Group;
        return $group->showCheckbox($groups);
    }

    public function getGroupPrice(Request $request)
    {
        parse_str($request->games, $gameIds);
        $total = 0;

        foreach ($gameIds['games'] as $id) {
            $game = Game::find($id);
            if (!$game) {
                continue;
            }
            $prices = $game->calcPrice(true, true);
            if (!array_key_exists('a_offer_price', $prices)) {
                return response()->json([
                    'message' => 'تعرفه ها به درستی تنظیم نشده اند.'
                ], 404);
            }
            $total += $game->calcPrice()['a_offer_price'];
        }
        return $total;
    }

    public function groupClose(Request $request)
    {
        // dd($request->all());
        $payment = $request->payment;
        parse_str($request->games, $gameIds);
        $setting = new Setting;
        $payment_type = $setting->getSetting('default_payment_type');
        foreach ($gameIds['games'] as $id) {
            $game = Game::find($id);
            if (!$game) {
                continue;
            }
            $using_sharj = false;
            $payments = [];
            if ($payment == "true") {
                $price = $game->calcPrice($using_sharj)['a_offer_price'];
                $payments = [
                    $payment_type => $price
                ];
            }
            $request->merge([
                'g_id' => $id,
                'payments' => $payments,
                'using_sharj' => $using_sharj
            ]);
            $this->close($request);
        }
    }

    // public function updateDeposit(Request $request)
    // {
    //     $id = $request->id;
    //     $deposit = $request->deposit_price ?? 0;
    //     $game = Game::find($id);
    //     if (!$game) {
    //         return response()->json([
    //             'message' => 'یافت نشد.'
    //         ], 404);
    //     }

    //     $game->deposit = $deposit;
    //     $game->save();
    //     return $game->loadGame($id);
    // }
}
