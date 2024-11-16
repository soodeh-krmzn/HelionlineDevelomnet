<?php

namespace App\Http\Controllers;

use App\Models\Admin\Account;
use App\Models\VoteResponse;
use Illuminate\Http\Request;

class VoteResponseController extends Controller
{
    public function index()
    {
        $response = new VoteResponse;
        return view('response.index', compact('response'));
    }

    public function store(Request $request, Account $account)
    {
        session(['db_name' => $account->db_name]);
        session(['db_user' => $account->db_user]);
        session(['db_pass' => $account->db_pass]);

        $mobile = $request->mobile;
        parse_str($request->answers, $answers);

        foreach ($answers as $question => $answer) {
            VoteResponse::create([
                'question_id' => $question,
                'mobile' => $mobile,
                'answer' => $answer
            ]);
        }
    }

    public function getItems(Request $request)
    {
        $vote_id = $request->vote_id;
        $response = new VoteResponse;
        return $response->showIndex($vote_id);
    }
}
