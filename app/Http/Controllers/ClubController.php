<?php

namespace App\Http\Controllers;

use App\Models\Club;
use App\Models\ClubLog;
use App\Models\Person;
use App\Services\Export;
use Carbon\Carbon;
use Hekmatinasser\Verta\Facades\Verta;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class ClubController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required',
            'price' => 'required',
            'rate' => 'required'
        ], [], [
            'price' => 'مبلغ پرداختی',
            'rate' => 'مبلغ شارژ'
        ]);

        $type = $request->type;
        $price = $request->price;
        $rate = $request->rate;
        $min_price = $request->min_price;
        $expire = $request->expire;
        $payment = $request->payment;
        $people = $request->people;

        $club = Club::updateOrCreate([
            'type' => $type
        ], [
            'price' => $price,
            'rate' => $rate,
            'min_price' => $min_price ?? 0,
            'expire' => $expire,
            'full_payment' => $payment,
            'all_people' => $people
        ]);
    }

    public function crud(Request $request)
    {
        $club = new Club;
        $type = $request->type;
        return $club->crud($type);
    }

    public function delete(Request $request)
    {
        $type = $request->type;
        $club = Club::where('type', $type)->first();
        if (!$club) {
            $club = new Club;
        }

        $club->delete();
        return $club->crud($type);
    }

    public function showRating()
    {
        $people = Person::where('rate', '>', 0)->orderBy('rate', 'desc')->get();
        return view('club.rating');
    }

    public function searchRating(Request $request)
    {
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

        $logs = ClubLog::query();
        if ($from_date != null) {
            $logs->where('created_at', '>=', $from_date);
        }
        if ($to_date != null) {
            $logs->where('created_at', '<=', $to_date);
        }

        $logs = $logs->groupBy('person_id')->selectRaw('person_id, sum(rate) as sum,created_at')->orderBy('sum', 'desc');
        return $logs;
    }

    public function export(Request $request)
    {
        $data = $this->searchRating($request)->get();
        return Export::export($data->map(function($item){
            $item['person_name']=$item->person?->getFullName();
            $item['balance_rate']=$item->sum;
            return $item;
        }), [
            'person_name', 'balance_rate'
        ]);
    }

    public function dataTable(Request $request)
    {
        if ($request->ajax()) {
            if ($request->has('filter_search')) {
                $data = $this->searchRating($request);
            } else {
                $data = ClubLog::groupBy('person_id')->selectRaw('person_id, sum(rate) as sum')->orderBy('sum', 'desc');
            }
            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('person_id', function(ClubLog $log) {
                    return $log->person?->getFullName() ?? '<span class="badge bg-danger">'.__("یافت نشد").'</span>';
                })
                ->editColumn('sum', '{{ cnf($sum) }}')
                ->rawColumns(["person_id"])
                ->make(true);
        }
    }
}
