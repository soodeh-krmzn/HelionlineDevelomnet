<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Club;
use App\Models\Person;
use App\Models\Wallet;
use App\Models\ClubLog;
use App\Models\Package;
use App\Models\Payment;
use App\Services\Export;
use App\Models\EditReport;
use App\Models\PaymentType;
use App\Exports\ExcelExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class PackageController extends Controller
{

    public function crud(Request $request, Package $group)
    {
        $package = new Package;
        $action = $request->action;
        if ($action == "create") {
            return $package->crud("create");
        } else if ($action == "update") {
            $id = $request->id;
            return $package->crud("update", $id);
        }
    }

    public function index()
    {
        $package = new Package;
        return view('package.index', compact('package'));
    }

    public function store(Request $request)
    {
        $package = new Package;
        $action = $request->action;
        $id = $request->id;
        $name = $request->name;
        $price = $request->price;
        $expire_time = $request->expire_time;
        $expire_day = $request->expire_day;
        if ($action == "create") {
            Package::create([
                'name' => $name,
                'price' => $price,
                'expire_time' => $expire_time,
                'expire_day' => $expire_day,
                'type' => $request->type
            ]);
        } else if ($action == "update") {
            $package = Package::find($id);
            $details = $this->editDetails($request, $package);
            $package->name = $name;
            $package->price = $price;
            $package->expire_time = $expire_time;
            $package->expire_day = $expire_day;
            $package->type = $request->type;
            if ($details != "") {
                EditReport::create([
                    'user_id' => auth()->user()->id,
                    'edited_type' => 'App\Models\Package',
                    'edited_id' => $id,
                    'details' => $details
                ]);
            }
            $package->save();
        }
        return $package->showIndex();
    }

    public function delete(Request $request)
    {
        $id = $request->id;
        $package = Package::find($id);
        $package->delete();
        return $package->showIndex();
    }

    public function charge()
    {
        $package = new Package;
        $packages = Package::all();
        return view('package.charge', compact('package', 'packages'));
    }

    public function storeCharge(Request $request)
    {
        $package = new Package;
        $action = $request->action;

        if ($action == "create") {
            $request->validate([
                'person'=>'required'
            ]);
            $person = Person::find($request->person);
            $package = Package::find($request->package);
            $oldSharj = 0;
            // dd($package->type!=$person->sharj_type,Carbon::create($person->expire)->gte(today()),$person );
            if($request->check and Carbon::create($person->expire)->gte(today()) and $person->sharj>0 and $package->type!=$person->sharj_type){
                return ['diffPack'=>true,'message'=>__('messages.diffPack',['originType'=>__($person->sharj_type),'destType'=>__($package->type)])];
            }
            // dd(Carbon::create($person->expire),today(),Carbon::create($person->expire)->gte(today()))
            if (Carbon::create($person->expire)->gte(today()) and $package->type == $person->sharj_type) {
                $oldSharj = $person->sharj;
            }
            $sharj = $package->expire_time + $oldSharj;
            $expire = today()->addDays($package->expire_day);
            parse_str($request->prices, $prices);
            parse_str($request->details, $details);

            $b = $person->balance;
            Payment::create([
                'user_id' => auth()->id(),
                'person_id' => $person->id,
                'price' => $package->price * (-1),
                'details' => "مبلغ شارژ بسته",
                'type' => "charge_package"
            ]);
            $b -= $package->price;

            $pay = Payment::storeAll($prices, $details, $person, null, null, "خرید بسته اشتراکی");
            $b += $pay['balance'];

            $club = Club::where('type', 'package')->first();
            if ($club) {
                $club->storeClub($person, $pay['clubable'], 'App\Models\Package',  $package->id);
            }

            $person->balance = $b;
            $person->pack = $package->id;
            $person->sharj = $sharj;
            $person->expire = $expire;
            $person->sharj_type=$package->type;
            $person->save();
        } else if ($action == "update") {
            $person = Person::find($request->id);
            $sharj = $request->sharj;
            $expire = today()->addDays($request->expire);
            $details = "تغییر شارژ از " . cnf($person->sharj) . " به " . cnf($request->sharj) . "," .
                "\n" .
                "تغییر تاریخ انقضای شارژ از  " . dateFormat($person->expire) . " به " . dateFormat($expire);

            $person->sharj = $sharj;
            $person->expire = $expire;
            $person->save();

            EditReport::create([
                'user_id' => auth()->user()->id,
                'edited_type' => 'App\Models\Person',
                'edited_id' => $person->id,
                'details' => $details
            ]);
        }
        $records = Person::whereNot('pack', null)->get();
        return $package->chargeList($records);
    }

    public function crudCharge(Request $request)
    {
        $id = $request->id;
        $action = $request->action;
        $p_id = $request->p_id;
        $package = new Package;
        return $package->crudCharge($id, $p_id, $action);
    }

    public function deleteCharge(Request $request)
    {
        $person = Person::find($request->id);
        $package = new Package;

        $details = "تغییر شارژ از " . cnf($person->sharj) . " به  صفر," .
            "\n" .
            "حذف تاریخ انقضای شارژ " . dateFormat($person->expire);

        $person->pack = null;
        $person->sharj = null;
        $person->expire = null;
        $person->save();

        EditReport::create([
            'user_id' => auth()->user()->id,
            'edited_type' => 'App\Models\Person',
            'edited_id' => $person->id,
            'details' => $details
        ]);


    }

    public function searchCharge(Request $request)
    {
        $person_id = $request->person_id;
        $package_id = $request->package_id;
        $charge = $request->charge;

        $records = Person::whereNot('pack', null);

        if ($person_id != null) {
            $records = $records->where('id', $person_id);
        }
        if ($package_id != null) {
            $records = $records->where('pack', $package_id);
        }
        if ($charge != null) {
            if ($charge == "yes") {
                $records = $records->where('sharj', '>', 0);
            } else if ($charge == "no") {
                $records = $records->whereNot('sharj', '>', 0);
            }
        }

        $records = $records;
        return $records;
    }

    public function reports()
    {
        $report = new EditReport;
        return view('package.report', compact('report'));
    }

    protected function editDetails(Request $request, Package $package)
    {
        $details = "";
        if ($request->name != $package->name) {
            $details .= "تغییر نام از " . $package->name . " به " . $request->name . ", \n";
        }
        if ($request->price != $package->price) {
            $details .= "تغییر قیمت از " . cnf($package->price) . " به " . cnf($request->price) . ", \n";
        }
        if ($request->expire_time != $package->expire_time) {
            $details .= "تغییر شارژ دقیقه از " . cnf($package->expire_time) . " به " . cnf($request->expire_time) . ", \n";
        }
        if ($request->expire_day != $package->expire_day) {
            $details .= "تغییر شارژ روز از " . cnf($package->expire_day) . " به " . cnf($request->expire_day) . ", \n";
        }
        return $details;
    }

    public function export()
    {
        return Export::export(Package::all()->map(function ($item) {
            $item['price_format'] = price($item['price']);
            return $item;
        }), ['name', 'price_format', 'expire_time', 'expire_day']);
    }

    public function exportCharge(Request $request)
    {
        $records = $this->searchCharge($request);
        return Export::export($records->map(function ($item) {
            $item['pack_name'] = $item->package->name;
            $item['expire'] = dateToLangExcel($item->expire);
            return $item;
        }), [
            'name', 'family', 'pack_name', 'sharj', 'expire'
        ]);
    }

    public function dataTable(Request $request)
    {
        if ($request->ajax()) {
            $data = Package::query();
            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('price', '{{ cnf($price) }}')
                ->editColumn('expire_time', '{{ cnf($expire_time) }}')
                ->editColumn('expire_day', '{{ cnf($expire_day) }}')
                ->editColumn('type', '{{ __($type) }}')
                ->addColumn('action', function (Package $package) {
                    $actionBtn = '';
                    if (Gate::allows('update')) {
                        $actionBtn .= '<button type="button" class="btn btn-warning btn-sm crud" data-action="update" data-id="' . $package->id . '" data-bs-target="#crud-modal" data-bs-toggle="modal"><i class="bx bx-edit"></i></button>';
                    }
                    if (Gate::allows('delete')) {
                        $actionBtn .= '
                        <button type="button" class="btn btn-danger btn-sm delete-package" data-id="' . $package->id . '"><i class="bx bx-trash"></i></button>';
                    }
                    return $actionBtn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
    }

    public function chargeTable(Request $request)
    {
        if ($request->ajax()) {
            if ($request->has('filter_search')) {
                $data = $this->searchCharge($request);
            } else {
                $data = Person::where('pack', '>', 0);
            }
            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('name', function (Person $person) {
                    return $person->getFullName() ?? '<span class="badge bg-danger">'.__("یافت نشد").'</span>';
                })
                ->editColumn('pack', function (Person $person) {
                    return $person->package?->name ?? '<span class="badge bg-danger">'.__("یافت نشد").'</span>';
                })
                ->editColumn('sharj', '{{ cnf($sharj) }}')
                ->editColumn('sharj_type', '{{__($sharj_type)}}')
                ->editColumn('sharj-hour', '{{ formatDuration($sharj) }}')
                ->editColumn('status', function(Person $person){
                    if (now()->startOfDay()->lte(Carbon::create($person->expire))) {
                        return '<span class="badge bg-success">' . __("معتبر") . '</span>';
                    } else {
                        return '<span class="badge bg-danger">' . __("منقضی ") . '</span>';
                    }
                })
                ->editColumn('expire', function (Person $person) {
                    return dateFormat($person->expire) ?? '-';
                })
                ->addColumn('action', function (Person $person) {
                    $actionBtn = '';
                    if (Gate::allows('update')) {
                        $actionBtn .= '
                        <button type="button" class="btn btn-warning btn-sm crud" data-action="update" data-id="' . $person->id . '" data-bs-target="#crud-modal" data-bs-toggle="modal"><i class="bx bx-edit"></i></button>';
                    }
                    if (Gate::allows('delete')) {
                        $actionBtn .= '
                        <button type="button" class="btn btn-danger btn-sm delete-charge" data-id="' . $person->id . '"><i class="bx bx-trash"></i></button>';
                    }
                    return $actionBtn;
                })
                ->rawColumns(['action', 'name', 'pack','status'])
                ->make(true);
        }
    }
}
