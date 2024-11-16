<?php

namespace App\Http\Controllers;

use App\Models\Admin\Account;
use App\Models\Vote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Yajra\DataTables\Facades\DataTables;

class VoteController extends Controller
{

    public function crud(Request $request, Vote $vote)
    {
        $vote = new Vote;
        $action = $request->action;
        if($action == "create") {
            return $vote->crud("create");
        } else if ($action == "update") {
            $id = $request->id;
            return $vote->crud("update", $id);
        }
    }

    public function index()
    {
        $vote = new Vote;
        return view('vote.index', compact('vote'));
    }

    public function store(Request $request)
    {
        $id = $request->id;
        $action = $request->action;
        $name = $request->name;
        $details = $request->details;
        $status = $request->status;
        if ($status == 1) {
            foreach (Vote::all() as $v) {
                $v->status = 0;
                $v->save();
            }
        }
        if ($action == "create") {
            $vote = Vote::create([
                'name' => $name,
                'details' => $details,
                'status' => $status
            ]);
        } else if ($action == "update") {
            $vote = Vote::find($id);
            $vote->name = $name;
            $vote->details = $details;
            $vote->status = $status;
            $vote->save();
        }
        return $vote->showIndex();
    }

    public function delete(Request $request)
    {
        $id = $request->id;
        $vote = Vote::find($id);
        $vote->delete();
        return $vote->showIndex();
    }

    public function voteForm(Account $account)
    {
        session(['db_name' => $account->db_name]);
        session(['db_user' => $account->db_user]);
        session(['db_pass' => $account->db_pass]);
        $vote = new Vote;
        return view('vote.form', compact('vote', 'account'));
    }

    public function dataTable(Request $request)
    {
        if ($request->ajax()) {
            $data = Vote::latest();
            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('status', function(Vote $vote) {
                    return $vote->getStatus();
                })
                ->addColumn('action', function(Vote $vote) {
                    $actionBtn = '<button type="button" class="btn btn-info btn-sm crud-questions" data-vote_id="' . $vote->id . '" data-bs-target="#crud" data-bs-toggle="modal"><i class="bx bx-question-mark"></i></button>';
                    if (Gate::allows('update')) {
                        $actionBtn .= '
                        <button type="button" class="btn btn-warning btn-sm crud" data-action="update" data-id="' . $vote->id . '" data-bs-target="#crud" data-bs-toggle="modal"><i class="bx bx-edit"></i></button>';
                    }
                    if (Gate::allows('delete')) {
                        $actionBtn .= '
                        <button type="button" class="btn btn-danger btn-sm delete-vote" data-id="' . $vote->id . '"><i class="bx bx-trash"></i></button>';
                    }
                    return $actionBtn;
                })
                ->rawColumns(['action', 'status'])
                ->make(true);
        }
    }

}
