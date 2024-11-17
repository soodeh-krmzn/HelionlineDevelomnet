<?php

namespace App\Http\Controllers;

use App\Models\Price;
use App\Models\Section;
use Illuminate\Http\Request;

class PriceController extends Controller
{

    public function priceForm(Request $request)
    {
        $price = new Price;
        $section_id = $request->id;
        $section=Section::select('id', 'type')->find($section_id);
        $formView=$price->priceTable($section_id);
        return ["form"=> $formView->render(),'section'=>$section];
    }

    public function storePrice(Request $request)
    {
        $validated = $request->validate([
            'section_id' => 'required',
            'entrance_price' => 'nullable|numeric',
            'from' => 'required|integer|max:2147483647',
            'to' => 'required|integer|max:2147483647',
            'calc_type' => 'required',
            'price' => 'required|numeric',
            'price_type' => 'required',
        ], [], [
            'from' => 'از دقیقه',
            'to' => 'تا دقیقه'
        ]);
        $action = $request->action;
        $id = $request->id;
        $section_id = $request->section_id;
        $entrance_price = $request->entrance_price ?? 0;
        $from = $request->from;
        $to = $request->to;
        $calc_type = $request->calc_type;
        $price = $request->price;
        $price_type = $request->price_type;

        if ($from >= $to) {
            return response()->json([
                'message' => 'بازه زمانی وارد شده معتبر نمیباشد.'
            ], 400);
        }

        $check1 = Price::whereNot('id', $id)
                ->where('section_id', $section_id)
                ->where('price_type', $price_type)
                ->where('from', '<', $from)
                ->where('to', '>', $from)
                ->first();
        $check2 = Price::whereNot('id', $id)
                ->where('section_id', $section_id)
                ->where('price_type', $price_type)
                ->where('from', '<', $to)
                ->where('to', '>', $to)
                ->first();
        $check3 = Price::whereNot('id', $id)
                ->where('section_id', $section_id)
                ->where('price_type', $price_type)
                ->where('from', '>=', $from)
                ->where('to', '<=', $to)
                ->first();
        if ($check1 || $check2 || $check3) {
            return response()->json([
                'message' => 'بازه زمانی وارد شده تکراری است.'
            ], 400);
        }

        if ($action == "create") {
            Price::create([
                'section_id' => $section_id,
                'entrance_price' => $entrance_price,
                'from' => $from,
                'to' => $to,
                'calc_type' => $calc_type,
                'price' => $price,
                'price_type' => $price_type
            ]);
        } else if ($action == "update") {
            $priceU = Price::find($id);
            $priceU->section_id = $section_id;
            $priceU->entrance_price = $entrance_price ?? 0;
            $priceU->from = $from;
            $priceU->to = $to;
            $priceU->calc_type = $calc_type;
            $priceU->price = $price;
            $priceU->price_type = $price_type;
            $priceU->save();
        }
        $priceM = new Price;
        return $priceM->priceTable($section_id);
    }

    public function deletePrice(Request $request)
    {
        $id = $request->id;
        $section_id = $request->section_id;
        $priceD = Price::find($id);
        $priceD->delete();

        $priceM = new Price;
        return $priceM->priceTable($section_id);
    }

}
