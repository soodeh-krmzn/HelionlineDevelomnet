<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\PersonMeta;
use Illuminate\Http\Request;

class PersonMetaController extends Controller
{
    public function store(Request $request)
    {
        $id = $request->id;
        parse_str($request->data, $data);
        foreach ($data as $key => $value) {
            $check = PersonMeta::where('p_id', $id)->where('meta', $key)->first();
            if ($check) {
                if ($value == "") {
                    $check->delete();
                } else {
                    $check->value = $value;
                    $check->save();
                }
            } else {
                if ($value != "") {
                    PersonMeta::create([
                        'p_id' => $id,
                        'meta' => $key,
                        'value' => $value
                    ]);
                }
            }
        }
        $game = new Game;
        return $game->showIndex();
    }

    public function crud(Request $request)
    {
        $id = $request->id;
        $meta = new PersonMeta;
        return $meta->crud($id);
    }

    public function crudGame(Request $request)
    {
        $p_id = $request->p_id;
        $meta = new PersonMeta;
        return $meta->crudGame($p_id);
    }
}
