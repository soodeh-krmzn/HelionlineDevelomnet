<?php

namespace App\Http\Controllers;

use App\Exports\ExcelExport;
use App\Models\Book;
use App\Models\BookLoan;
use App\Models\EditReport;
use App\Services\Export;
use Hekmatinasser\Verta\Verta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class BookLoanController extends Controller
{

    public function crud(Request $request, BookLoan $bookLoan)
    {
        $bookLoan = new BookLoan;
        $action = $request->action;
        if ($action == "create") {
            return $bookLoan->crud("create");
        } else if ($action == "update") {
            $id = $request->id;
            return $bookLoan->crud("update", $id);
        }
    }

    public function index()
    {
        $loan = new BookLoan;
        $book_loans = BookLoan::all();
        return view('library.loan', compact('loan', 'book_loans'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'person_id'=>'required'
        ]);
        $loan = new BookLoan;
        $action = $request->action;
        $id = $request->id;
        $user_id = auth()->id();
        $book_id = $request->book_id;
        $person_id = $request->person_id;
        $status = $request->status;
        $details = $request->details;
        if ($request->return_date != null) {
            $return_date = dateSetFormat($request->return_date);
        } else {
            $return_date = null;
        }
        if ($action == "create") {
            $loan = BookLoan::create([
                'user_id' => $user_id,
                'book_id' => $book_id,
                'person_id' => $person_id,
                'return_date' => $return_date,
                'status' => $status,
                'details' => $details
            ]);
        } else if ($action == "update") {
            $loan = BookLoan::find($id);
            $edit_details = $this->editDetails($request, $loan);
            $loan->person_id = $person_id;
            $loan->book_id = $book_id;
            $loan->return_date = $return_date;
            $loan->status = $status;
            $loan->details = $details;

            if ($edit_details != "") {
                EditReport::create([
                    'user_id' => auth()->user()->id,
                    'edited_type' => 'App\Models\BookLoan',
                    'edited_id' => $id,
                    'details' => $edit_details
                ]);
            }
            $loan->save();
        }
        return $loan->showIndex(BookLoan::all());
    }

    public function delete(Request $request)
    {
        $id = $request->id;
        $loan = BookLoan::find($id);
        $loan->delete();
        return $loan->showIndex(BookLoan::all());
    }

    public function export()
    {
        $bookLoans = BookLoan::latest()->get()->map(function ($item) {
            $item['status_msg'] = $item['status'] == 1 ? __("excel.loanReturnMsg") : __("excel.loanNotReturnMsg");
            $item['person_name']=$item->person?->getFullName();
            $item['book_name']=$item->book?->name;
            $item['return_date_format']=app()->getLocale()=='fa'?persianTime($item['return_date']):gregorianTime($item['return_date']);
            return $item;
        });

        return Export::export($bookLoans,['book_name','person_name','status_msg','details','return_date_format']);
    }

    protected function editDetails(Request $request, BookLoan $loan)
    {
        $details = "";
        if ($request->person_id != $loan->person_id) {
            $details .= "تغییر کد شخص از " . $loan->person_id . " به " . $request->person_id . " , \n";
        }
        if ($request->book_id != $loan->book_id) {
            $details .= "تغییر کد کتاب از " . $loan->book_id . " به " . $request->book_id . " , \n";
        }
        if ($request->book_id != $loan->book_id) {
            $details .= "تغییر کد کتاب از " . $loan->book_id . " به " . $request->book_id . " , \n";
        }
        if ($request->return_date != null) {
            $return_date = $request->return_date;
        } else {
            $return_date = null;
        }
        if ($return_date != $loan->return_date) {
            $details .= "تغییر تاریخ بازگشت از " . DateFormat($loan->return_date). " به " . $return_date . " , \n";
        }

        return $details;
    }

    public function dataTable(Request $request)
    {
        if ($request->ajax()) {
            $data = BookLoan::query();
            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('created_at', function (BookLoan $loan) {
                    // return Verta($loan->created_at)->format("Y/m/d - H:i");
                    return dateFormat($loan->created_at);
                })
                ->editColumn('status', function (BookLoan $loan) {
                    return $loan->getStatusLabel();
                })
                ->editColumn('return_date', function (BookLoan $loan) {

                    return dateFormat($loan->return_date)??'-';
                })
                ->addColumn('user', function (BookLoan $loan) {
                    return $loan->user?->getFullName() ?? '<span class="badge bg-danger">'.__("یافت نشد").'</span>';
                })
                ->addColumn('person', function (BookLoan $loan) {
                    return $loan->person?->getFullName() ?? '<span class="badge bg-danger">'.__("یافت نشد").'</span>';
                })
                ->addColumn('book', function (BookLoan $loan) {
                    return $loan->book?->name ?? '<span class="badge bg-danger">'.__("یافت نشد").'</span>';
                })
                ->addColumn('action', function (BookLoan $loan) {
                    $actionBtn = '';
                    if (Gate::allows('update')) {
                        $actionBtn .= '<button type="button" class="btn btn-warning btn-sm crud" data-action="update" data-id="' . $loan->id . '" data-bs-target="#crud" data-bs-toggle="modal"><i class="bx bx-edit"></i></button>';
                    }
                    if (Gate::allows('delete')) {
                        $actionBtn .= '
                        <button type="button" class="btn btn-danger btn-sm delete-loan" data-id="' . $loan->id . '"><i class="bx bx-trash"></i></button>';
                    }
                    return $actionBtn;
                })
                ->rawColumns(['action', 'status', 'user', 'person', 'book'])
                ->make(true);
        }
    }
}
