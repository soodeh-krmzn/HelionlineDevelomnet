<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Services\SMS;
use App\Models\Person;
use App\Models\Payment;
use App\Models\Setting;
use App\Services\Export;
use App\Exports\ExcelExport;
use Illuminate\Http\Request;
use Hekmatinasser\Verta\Verta;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class PersonController extends Controller
{
    public function index()
    {
        return view('person.index');
    }
    public function translate()
    {
        return view('superadmin.translate2');
    }

    public function crud(Request $request)
    {
        $action = $request->action;
        $id = $request->id;
        $person = new Person;
        return $person->crud($action, $id);
    }

    public function getPersonNote(Request $request)
    {
        $person = Person::find($request->id);

        if ($person->getMeta("person-info")) {
            return  '<div class="alert alert-danger text-center">' . $person->getMeta("person-info") . '</div>';
        }
        return '';
    }

    public function store(Request $request)
    {
        $setting = new Setting;
        $mobileCount = $setting->mobileCount();
        $repetitiveMobile = $setting->getSetting('repetitive-mobile');

        $validated = $request->validate([
            'name' => 'required',
            'family' => 'required',
            'gender' => 'required',
            'national_code' => ['nullable', Rule::unique('people')->ignore($request->id)->where(function ($query) {
                return $query->whereNull('deleted_at'); // Exclude soft-deleted records
            })],
            'mobile' => ['required', 'digits:' . $mobileCount, $repetitiveMobile != 'true' ?  Rule::unique('people')->ignore($request->id)->where(function ($query) {
                return $query->whereNull('deleted_at');
            }) : ''],
        ]);

        if ($request->birth != null) {
            $birth = dateSetFormat($request->birth);
        } else {
            $birth = null;
        }

        try {
            $action = $request->action;
            if ($action == "create") {
                $person = new Person;
                $person->created_by = 1;
                $person->name = $request->name;
                $person->family = $request->family;
                $person->mobile = $request->mobile;
                $person->birth = $birth;
                $person->shamsi_birth = $request->birth;
                $person->gender = $request->gender;
                $person->national_code = $request->national_code;
                $person->reg_code = $request->reg_code;
                $person->club = $request->club;
                $person->save();
            } else if ($action == "update") {
                $id = $request->id;
                $person = Person::find($id);
                $person->created_by = 1;
                $person->name = $request->name;
                $person->family = $request->family;
                $person->mobile = $request->mobile;
                $person->birth = $birth;
                $person->shamsi_birth = $request->birth;
                $person->gender = $request->gender;
                $person->national_code = $request->national_code;
                $person->reg_code = $request->reg_code;
                $person->club = $request->club;
                $person->save();
            }
            // $person = new Person;
            // return $person->showIndex(Person::all());
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function delete(Request $request)
    {
        $id = $request->id;
        $person = Person::find($id);
        if ($person->activeGame()) {
            return response()->json([
                'message' => 'این شخص در مجموعه حضور دارد. لطفا قبل از حذف کردن، خروج را ثبت کنید.'
            ], 409);
        }
        $person->delete();
        // return $person->showIndex(Person::all());
    }

    public function export(Request $request)
    {
        $data = $this->search($request)->get();
        return Export::export($data->map(function ($item) {
            $item['birth_date'] = dateToLangExcel($item->birth);
            return $item;
        }), ['id', 'name', 'family', 'birth_date', 'reg_code', 'mobile', 'national_code', 'sharj']);
    }

    public function search(Request $request, $today = false)
    {
        $name = $request->name;
        $family = $request->family;
        $mobile = $request->mobile;
        $national_code = $request->national_code;
        $gender = $request->gender;
        $reg_code = $request->reg_code;
        $day = $request->day ? str_pad($request->day, 2, "0", STR_PAD_LEFT) : '';
        $month = $request->month ? str_pad($request->month, 2, "0", STR_PAD_LEFT) : '';

        if (!is_null($request->from_birthday)) {
            $from_birthday = Carbon::parse(dateSetFormat($request->from_birthday));
        } else {
            $from_birthday = null;
        }
        if (!is_null($request->to_birthday)) {
            $to_birthday = Carbon::parse(dateSetFormat($request->to_birthday, 1));
        } else {
            $to_birthday = null;
        }

        $people = Person::query();

        if ($name != null) {
            $people->where('name', 'like', '%' . $name . '%');
        }
        if ($family != null) {
            $people->where('family', 'like', '%' . $family . '%');
        }
        if ($mobile != null) {
            $people->where('mobile', 'like', '%' . $mobile . '%');
        }
        if ($national_code != null) {
            $people->where('national_code', 'like', '%' . $national_code . '%');
        }
        if ($reg_code != null) {
            $people->where('reg_code', 'like', '%' . $reg_code . '%');
        }
        if ($gender != null) {
            $people->where('gender', $gender);
        }
        if ($from_birthday != null) {
            $people->where('birth', '>=', $from_birthday);
        }
        if ($to_birthday != null) {
            $people->where('birth', '<=', $to_birthday);
        }

        if ($today) {
            $day = str_pad(getDay(), 2, "0", STR_PAD_LEFT);
            $month =  str_pad(getMonth(), 2, "0", STR_PAD_LEFT);
        }
        if ($day != null) {
            $day = ltrim($day, '0');
            $people->where(function ($query) use ($day) {
                if (strlen($day) == 2) {
                    $query->where('shamsi_birth', 'like', '____/%_/' . $day);
                } else {
                    $query->where('shamsi_birth', 'like', '____/%_/0' . $day)->orWhere('shamsi_birth', 'like', '____/%_/' . $day);
                }
            });
        }
        if ($month != null) {
            $month = ltrim($month, '0');
            $people->where(function ($query) use ($month) {
                if(strlen($month==2)){
                    $query->where('shamsi_birth', 'LIKE', '____/' . $month . '/%_');
                }else{
                    $query->where('shamsi_birth', 'LIKE', '____/0' . $month . '/%_')
                    ->orWhere('shamsi_birth', 'LIKE', '____/' . $month . '/%_');
                }
            });
        }
        // $people = $people->latest();
        return $people;
    }

    public function dataTable(Request $request)
    {
        if ($request->ajax()) {
            if ($request->has('filter_search')) {
                $data = $this->search($request);
            } else {
                $data = Person::query();
            }
            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('created_at', function (Person $person) {
                    // return Verta($person->created_at)->format("Y/m/d H:i");
                    return timeFormat($person->created_at);
                })
                ->editColumn('birth', function (Person $person) {
                    return $person->birth ? dateFormat($person->birth) : (dateFormat($person->shamsi_birth) ?? '');
                })
                ->editColumn('gender', function (Person $person) {
                    return $person->getGenderLabel($person->gender);
                })
                ->addColumn('action', function (Person $person) {
                    $actionBtn = '';
                    if (Gate::allows('update')) {
                        $actionBtn .= '
                        <button type="button" class="btn btn-info btn-sm meta" data-id="' .  $person->id . '" data-bs-toggle="modal" data-bs-target="#meta"><i class="bx bx-user"></i></button>
                        <button type="button" class="btn btn-warning btn-sm crud" data-action="update" data-id="' . $person->id . '" data-bs-toggle="offcanvas" data-bs-target="#offcanvasAddUser"><i class="bx bx-edit"></i></button>';
                    }
                    if (Gate::allows('delete')) {
                        $actionBtn .= '
                        <button class="btn btn-danger btn-sm" id="delete-person" data-id="' . $person->id . '"><i class="bx bx-trash"></i></button>';
                    }
                    return $actionBtn;
                })->addColumn('action2', function (Person $person) {
                    $btn = '<button type="button" class="btn btn-success btn-sm send-sms" data-action="single" data-id="' .  $person->id . '">ارسال پیامک</button>';
                    return $btn;
                })->addColumn('lastSend', function (Person $person) {
                    $smsInfo = $person->LastBirthSmsInfo();
                    return $smsInfo ? $smsInfo['lastDate'] . " <small class='badge bg-info'>(" . $smsInfo['total'] . ")</small>" : "بدون ارسال";
                    // $btn = '<button type="button" class="btn btn-success btn-sm send-sms" data-action="single" data-id="' .  $person->id . '">ارسال پیامک</button>';
                    // return $btn;
                })
                ->rawColumns(['action', 'gender', 'action2', 'lastSend'])
                ->make(true);
        }
    }

    public function report(Request $request)
    {
        $person = Person::find($request->id) ?? new Person;
        return view('person.report', compact('person'));
    }

    public function sps(Request $request)
    {
        $search = $request->search;
        $persons = Person::select(['id', 'name', 'family'])->where(DB::raw("CONCAT(name, ' ', family)"), "like", "%" . $search . "%")->get();

        return response()->json([$persons]);
    }

    public function searchReport(Request $request)
    {
        $tab = $request->tab;

        switch ($tab) {
            case "payment":
                $p = new PaymentController();
                return $p->search($request);
            case "game":
                $g = new GameController();
                return $g->searchReport($request);
            case "factor":
                $f = new FactorController();
                return $f->search($request);
            case "wallet":
                $w = new WalletController();
                return $w->search($request);
            case "club":
                $c = new ClubLogController();
                return $c->search($request);
        }
    }

    public function debt()
    {
        $person = new Person;
        return view('person.debt', compact('person'));
    }

    public function birthday()
    {
        return view('person.birthday');
    }

    public function birthdaySms(Request $request)
    {
        $id = $request->id;
        $action = $request->action;

        if ($action == "single") {
            $person = Person::find($id);
            $mobile = $person?->mobile;
            if (!$person || !$mobile) {
                return response()->json([
                    'message' => 'یافت نشد.'
                ], 404);
            }

            $sms = new SMS();
            $sms->send_sms("تولد", $mobile, [
                "name" => $person->name
            ]);
        } else if ($action == "all") {
            // dd(request()->all());
            if ($request->day or $request->month or $request->from_birthday or $request->to_birthday) {
                $people = $this->search($request);
            } else {
                $people = $this->search($request, true);
            }
            $sms = new SMS();
            foreach ($people as $person) {
                $sms->send_sms("تولد", $person->mobile, [
                    "name" => $person->name
                ]);
            }
        }
    }

    public function crudBirthday()
    {
        $person = new Person;
        return $person->crudBirthday();
    }

    public function storeBirthday(Request $request)
    {
        if ($request->days == '') {
            $setting = Setting::where('meta_key', 'birthday_sms_days')->first();
            if ($setting) {
                $setting->delete();
            }
            return;
        }
        Setting::updateOrCreate([
            'meta_key' => 'birthday_sms_days'
        ], [
            'meta_value' => $request->days
        ]);
    }

    public function removeDebt(Request $request)
    {
        $action = $request->action;
        $id = $request->id;

        if ($action == "all") {
            $people = Person::where('balance', '<', 0)->get();
        } else if ($action == "single") {
            $people = Person::where('id', $id)->where('balance', '<', 0)->get();
        }

        if (!$people) {
            return response()->json([
                'message' => 'یافت نشد!'
            ], 404);
        }

        foreach ($people as $person) {
            $balance = abs($person->balance);
            Payment::create([
                "user_id" => auth()->id(),
                "person_id" => $person->id,
                "price" => $balance,
                "details" => 'تسویه حساب',
                "type" => 'remove_debt',
            ]);

            $person->balance = 0;
            $person->save();
        }

        $person = new Person;
        return $person->showDebt();
    }

    public function searchDebt(Request $request)
    {
        $person_id = $request->person_id;
        $from_price = $request->from_price * (-1);
        $to_price = $request->to_price * (-1);

        $people = Person::query()->where('balance', '<', 0);

        if ($person_id != null) {
            $people->where('id', $person_id);
        }
        if ($from_price != null) {
            $people->where('balance', '>=', $from_price);
        }
        if ($to_price != null) {
            $people->where('balance', '<=', $to_price);
        }

        $people = $people->get();
        return $people;
    }

    public function debtTable(Request $request)
    {
        if ($request->ajax()) {
            if ($request->has('filter_search')) {
                $data = $this->searchDebt($request);
            } else {
                $data = Person::where('balance', '<', 0)->get();
            }
            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('name', function (Person $person) {
                    return $person->getFullName();
                })
                ->editColumn('balance', '{{ cnf($balance) }}')
                ->addColumn('action', function (Person $person) {
                    $actionBtn = '<a href="' . route('reportPerson', ['id' => $person->id]) . '" class="btn btn-sm btn-info">گزارش حساب</a>';
                    if (request()->user()->can('unlimited')) {
                        $actionBtn .= '<button class="btn btn-sm btn-warning remove-debt mx-1" data-id="' . $person->id . '" data-balance="' . $person->balance . '" data-person_name="' . $person->getFullName() . '"  data-bs-toggle="modal" data-bs-target="#crud">تسویه حساب</button>';
                    }
                    return $actionBtn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
    }

    public function exportDebt(Request $request)
    {
        $data = $this->searchDebt($request);
        return Export::export($data->map(function ($item) {
            $item['balance'] = price($item->balance);
            $item['wallet_value'] = price($item->wallet_value);
            return $item;
        }), [
            'id',
            'name',
            'family',
            'birth_date',
            'reg_code',
            'mobile',
            'national_code',
            'sharj',
            'address',
            'rate',
            'wallet_value',
            'balance'
        ]);
    }

    public function getSumDebt(Request $request)
    {
        if ($request->has('filter_search')) {
            $data = $this->searchDebt($request);
        } else {
            $data = Person::where('balance', '<', 0)->get();
        }
        if ($data->count() <= 0) {
            return 0;
        }
        return $data->sum('balance');
    }
}
