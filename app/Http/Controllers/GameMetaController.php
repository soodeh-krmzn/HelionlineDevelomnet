<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Game;
use App\Models\GameMeta;
use Illuminate\Http\Request;
use Hekmatinasser\Verta\Verta;

class GameMetaController extends Controller
{
    public function crud(Request $request)
    {
        if ($request->target == 'edit-meta-period') {
            $end = null;
            if (app()->getLocale() == 'fa') {
                if ($request->start) {
                    //with date
                    if (strlen($request->start) > 7) {
                        $start = Verta::parse($request->start)->formatGregorian('Y-m-d H:i:s');
                        $start = Carbon::create($start);
                    } else {
                        $start = Carbon::create($request->start);
                    }
                }
                if ($request->end) {
                    //with date
                    if (strlen($request->end) > 7) {
                        $end = Verta::parse($request->end)->formatGregorian('Y-m-d H:i:s');
                        $end = Carbon::create($end);
                    } else {
                        $end = Carbon::create($request->end);
                    }
                }
            } else {
                $start = Carbon::create($request->start);
                if ($request->end)
                    $end = Carbon::create($request->end);
            }
            if ($request->end) {
                if ($start->gte($end)) {
                    return response()->json(['message' => 'زمان پایان نمی تواند کوچک تر از زمان شروع باشد']);
                }
            }
            $meta = GameMeta::find($request->id);
            $meta->update([
                'start' => $start,
                'end' => $end
            ]);
            return ['g_id' => $meta->g_id];
        }
        $g_id = $request->g_id;
        $meta = new GameMeta;
        return $meta->showIndex($g_id, false);
    }
    public function edit(Request $request)
    {
        $meta = GameMeta::find($request->id);
        return view('includes.editMeta', compact('meta'));
    }

    public function crudGame(Request $request)
    {
        $g_id = $request->g_id;
        $meta = new GameMeta;
        return $meta->crudGame($g_id);
    }

    public function store(Request $request)
    {

        $game_id = $request->g_id;
        $game = Game::find($game_id);
        $person = $game->person;
        if ($person->sharj_type == 'times' && $person->isNotExpired() && ($request->count + $game->count) > $person->sharj) {
            return response()->json([
                'message' => __("تعداد افراد وارد شده بیش از مرتبه شارژ موجود است.") . PHP_EOL . __("(حداکثر $person->sharj نفر)")
            ], 400);
        }
        if ($request->type == 'update-deposit') {
            $game->deposit = $request->value;
            $game->deposit_type = $request->paymentType;
            $game->save();
            return $game_id;
        }
        $count = $request->count;
        $type = $request->type;
        // dd($game->getLastUId());
        $meta = GameMeta::create([
            'g_id' => $game_id,
            'key' => $type,
            'value' => $count,
            'start' => now(),
            'u_id' => $game->getLastUId() + 1
        ]);

        $game->count += $count;
        $game->save();

        return $meta->showIndex($game_id);
    }

    public function update(Request $request)
    {
        $id = $request->id;
        $action = $request->action;
        $type = $request->type;
        $meta = GameMeta::find($id);

        if ($action == "type") {
            $meta->key = $type;
            $meta->save();
        } else if ($action == "pause") {
            $meta->end = now();
            $meta->save();
        } else if ($action == "play") {
            $meta->close = 1;
            $meta->save();
            GameMeta::create([
                'g_id' => $meta->g_id,
                'key' => $meta->key,
                'value' => $meta->value,
                'u_id' => $meta->u_id,
                'start' => now(),
            ]);
        }

        return $meta->showIndex($meta->g_id, $action);
    }

    public function delete(Request $request)
    {
        $id = $request->id;
        $meta = GameMeta::find($id);
        $totalMetas = GameMeta::where(['u_id' => $meta->u_id, 'g_id' => $meta->g_id])->count();
        $g_id = $meta->g_id;
        $count = $meta->value;
        $meta->delete();
        if ($totalMetas == 1) {
            $game = Game::find($g_id);
            $game->count -= $count;
            $game->save();
        }


        return $meta->showIndex($g_id);
    }
}
