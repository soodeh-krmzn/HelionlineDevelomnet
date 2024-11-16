<?php

namespace App\Http\Controllers;

use App\Models\EditReport;
use App\Models\User;
use App\Services\Export;
use Carbon\Carbon;
use Hekmatinasser\Verta\Facades\Verta;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class EditReportController extends Controller
{
    public function index($model)
    {

        $report = new EditReport;
       // dd($model,$report->models);
        if (!array_key_exists($model, $report->models)) {
            return abort(404);
        }

        $users = User::getUsers();
        return view('edit-reports.index', compact('users', 'model', 'report'));
    }

    public function search(Request $request)
    {
        $model = $request->model;
        $user_id = $request->user_id;

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

        $report = new EditReport;
        $reports = EditReport::where('edited_type', $report->models[$model]);

        if ($user_id != null) {
            $reports->where('user_id', $user_id);
        }
        if ($from_date != null) {
            $reports->where('created_at', '>=', $from_date);
        }
        if ($to_date != null) {
            $reports->where('created_at', '<=', $to_date);
        }

        $reports = $reports->latest()->get();
        return $reports;
    }

    public function export(Request $request)
    {

        $data = $this->search($request);
        switch ($request->model) {
            case 'charge':
                return Export::export($data->map(function ($item) {
                    $item['user_name'] = $item->user?->getFullName();
                    $item['person_name'] = $item->getEditedName();
                    return $item;
                }), ['user_name', 'person_name', 'details']);
                break;
            case 'game':
                return Export::export($data->map(function ($item) {
                    $item['user_name'] = $item->user?->getFullName();
                    $item['game_name'] = $item->getEditedName();
                    return $item;
                }), ['user_name', 'game_name', 'details']);
                break;
            case 'product':
                return Export::export($data->map(function ($item) {
                    $item['user_name'] = $item->user?->getFullName();
                    $item['product_name'] = $item->getEditedName();
                    return $item;
                }), ['user_name', 'product_name', 'details']);
                break;
            case 'loan':
                return Export::export($data->map(function ($item) {
                    $item['user_name'] = $item->user?->getFullName();
                    $item['book_name'] = $item->getEditedName();
                    return $item;
                }), ['user_name', 'book_name', 'details']);
                break;
            case 'offer':
                return Export::export($data->map(function ($item) {
                    $item['user_name'] = $item->user?->getFullName();
                    $item['offer_name'] = $item->getEditedName();
                    return $item;
                }), ['user_name', 'offer_name', 'details']);
                break;
        }
        return Export::export($data->map(function ($item) {
            $item['user_name'] = $item->user?->getFullName();
            $item['pack_name'] = $item->getEditedName();
            return $item;
        }), ['user_name', 'pack_name', 'details']);
    }

    public function dataTable(Request $request)
    {
        if ($request->ajax()) {
            if ($request->has('filter_search')) {
                $data = $this->search($request);
            } else {
                $report = new EditReport;
                $data = EditReport::where('edited_type', $report->models[$request->model]);
            }
            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('created_at', function (EditReport $report) {
                    return timeFormat($report->created_at);
                })
                ->addColumn('user', function (EditReport $report) {
                    return $report->user?->getFullName() ?? '<span class="badge bg-danger">'.__("یافت نشد").'</span>';
                })
                ->addColumn('name', function (EditReport $report) {
                    return $report->getEditedName() ?? '<span class="badge bg-danger">'.__("یافت نشد").'</span>';
                })
                ->rawColumns(['user', 'name'])
                ->make(true);
        }
    }
}
