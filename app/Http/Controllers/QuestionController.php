<?php

namespace App\Http\Controllers;

use App\Models\Question;
use Illuminate\Http\Request;

class QuestionController extends Controller
{

    public function crud(Request $request, Question $question)
    {
        $question = new Question;
        $id = $request->id;
        $vote_id = $request->vote_id;
        return $question->crud($id, $vote_id);
    }

    public function index()
    {
        $question = new Question;
        return view('question.index', compact('question'));
    }

    public function store(Request $request)
    {
        $question = new Question;
        $action = $request->action;
        $id = $request->id;

        if ($action == "create") {
            Question::create([
                'vote_id' => $request->vote_id,
                'title' => $request->title,
                'type' => $request->type,
                'display_order' => $request->display_order
            ]);
        } else if ($action == "update") {
            $list = Question::find($id);
            $list->title = $request->title;
            $list->type = $request->type;
            $list->display_order = $request->display_order;
            $list->save();
        }
        return $question->crud($request->id, $request->vote_id);
    }

    public function delete(Request $request)
    {
        $id = $request->id;
        $question = Question::find($id);
        $vote_id = $question->vote_id;
        $question->delete();
        return $question->crud(0, $vote_id);
    }

    public function getResult(Request $request)
    {
        $id = $request->question_id;
        $question = Question::find($id);

        if (!$question) {
            return response()->json([
                'message' => 'یافت نشد.'
            ], 404);
        }

        $r1 = 0;
        $r2 = 0;
        $r3 = 0;
        $r4 = 0;
        $r5 = 0;

        foreach ($question->responses as $response) {
            $v = 'r' . $response->answer;
            $$v++;
        }

        $answers = [
            $r1, $r2, $r3, $r4, $r5
        ];

        $result = [
            'name' => $question->title,
            'data' => $answers
        ];

        return $result;
    }
}
