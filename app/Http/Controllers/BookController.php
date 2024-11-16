<?php

namespace App\Http\Controllers;

use App\Exports\ExcelExport;
use App\Models\Book;
use App\Services\Export;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class BookController extends Controller
{
    public function crud(Request $request, Book $book)
    {
        $book = new Book;
        $action = $request->action;
        if($action == "create") {
            return $book->crud("create");
        } else if ($action == "update") {
            $id = $request->id;
            return $book->crud("update", $id);
        }
    }

    public function index()
    {
        $book = new Book;
        return view('library.book', compact('book'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required'
        ]);

        $action = $request->action;
        $id = $request->id;
        $name = $request->name;
        $cage = $request->cage;
        $cage_location = $request->cage_location;
        $author = $request->author;
        $publisher = $request->publisher;
        $details = $request->details;
        if ($action == "create") {
            $book = Book::create([
                'name' => $name,
                'cage' => $cage,
                'cage_location' => $cage_location,
                'author' => $author,
                'publisher' => $publisher,
                'details' => $details
            ]);
        } else if($action == "update") {
            $book = Book::find($id);
            $book->name = $request->name;
            $book->cage = $request->cage;
            $book->cage_location = $request->cage_location;
            $book->author = $request->author;
            $book->publisher = $request->publisher;
            $book->details = $request->details;
            $book->save();
        }
        return $book->showIndex();
    }

    public function delete(Request $request)
    {
        $id = $request->id;
        $book = Book::find($id);
        $book->delete();
        return $book->showIndex();
    }

    public function export()
    {
        $books=Book::all();
        return Export::export($books,['name','cage','cage_location','author','publisher','details']);
    }

    public function dataTable(Request $request)
    {
        if ($request->ajax()) {
            $data = Book::query();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function(Book $book) {
                    $actionBtn = '';
                    if (Gate::allows('update')) {
                        $actionBtn .= '<button type="button" class="btn btn-warning btn-sm crud" data-action="update" data-id="' . $book->id . '" data-bs-target="#crud" data-bs-toggle="modal"><i class="bx bx-edit"></i></button>';
                    }
                    if (Gate::allows('delete')) {
                        $actionBtn .= '
                            <button type="button" class="btn btn-danger btn-sm delete-book" data-id="' . $book->id . '"><i class="bx bx-trash"></i></button>';
                    }
                        return $actionBtn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
    }

}
