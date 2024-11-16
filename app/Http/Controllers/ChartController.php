<?php

namespace App\Http\Controllers;

use App\Models\Factor;
use App\Models\Game;
use App\Models\Payment;
use App\Models\PaymentType;
use App\Models\Section;
use App\Models\CostCategory;
use DateTime;
use Hekmatinasser\Verta\Verta;
use Illuminate\Http\Request;

class ChartController extends Controller
{
    public function index()
    {
        /*
        $total_payments = Payment::where('price', '>', 0)->get()->sum('price');
        $total_factors = Factor::all()->sum('final_price');
        $games = Game::all();
        $total_games = 0;
        foreach ($games as $game) {
            $total_games += $game->total_price;
            $total_games += $game->total_vip_price;
            $total_games += $game->login_price;
        }
        */
        //return view('chart.index', compact('total_payments', 'total_factors', 'total_games'));


        $sections = Section::all();
        $sectionModel = new Section;

        $paymentTypes = PaymentType::all();
        $paymentTypeModel = new PaymentType;

        $costCategories = CostCategory::all();
        $costCategoryModel = new CostCategory;

        return view('chart.index', compact('sections', 'sectionModel', 'paymentTypes', 'paymentTypeModel', 'costCategories', 'costCategoryModel'));
    }

    function getData(Request $request)
    {
        if (!is_null($request->from_date) && !is_null($request->to_date)) {
            $from_date = dateSetFormat($request->from_date);
            $to_date = dateSetFormat($request->to_date,1);
            $days = (new DateTime($from_date))->diff(new DateTime($to_date))->format('%a');
        } else {
            $from_date = now()->subDays(30);
            $to_date = now()->addDay();
            $days = 31;
        }

        $costCategories = array();
        foreach (CostCategory::all() as $category) {
            $costCategories[] = [
                'name' => $category->name,
                'data' => $category->getCostPaymentsList($from_date, $to_date)
            ];
        }

        $sections = array();
        foreach (Section::all() as $section) {
            $sections[] = [
                'name' => $section->name,
                'data' => $section->getMonthVisitList($from_date, $to_date)
            ];
        }

        $paymentTypes = array();
        foreach (PaymentType::all() as $paymentType) {
            $paymentTypes[] = [
                'name' => $paymentType->label,
                'data' => $paymentType->getMonthPaymentsList($from_date, $to_date)
            ];
        }

        $categories = array();
        $copy_from_date = $from_date->copy();
        for ($i = 1; $i <= $days; $i++) {
            $categories[] = DateFormat($copy_from_date);
            $copy_from_date->addDay();
        }

        return compact('costCategories', 'sections', 'paymentTypes', 'categories');
    }

    public function analyticIndex()
    {
        return view('chart.analytic');
    }

    public function analyticData(Request $request)
    {
        if (!is_null($request->day) && !is_null($request->month) && !is_null($request->year)) {
            // $date = Verta::parse($request->year.'/'.$request->month.'/'.$request->day)->toCarbon();
            $date = dateSetFormat($request->year.'/'.$request->month.'/'.$request->day);
        } else {
            $date = today();
        }

        $sections = array();
        $sections2 = array();
        foreach (Section::all() as $section) {
            $sections[] = [
                'name' => $section->name,
                'data' => $section->getHourlyVisitList($date)
            ];
            $sections2[] = [
                'name' => $section->name,
                'data' => $section->getHoursOfVisitList($date)
            ];
        }
        $categories = array();
        for ($i = 0; $i < 24; $i++) {
            $categories[] = $i;
        }

        return compact('sections', 'sections2', 'categories');

    }

}
