<?php

namespace App\Http\Controllers;

use App\Models\Cost;
use App\Models\CostCategory;
use App\Models\Game;
use App\Models\Factor;
use App\Models\Payment;
use App\Models\PaymentType;
use Illuminate\Http\Request;
use Hekmatinasser\Verta\Verta;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        return view('report.index', $this->getSum($request));
    }

    public function search(Request $request)
    {
        $fromDate=$request->from_date;
        $toDate=$request->to_date;
        // dd($fromDate,$toDate) ;
        // $day = $request->day;
        // $month = $request->month;
        // $year = $request->year;

        // $date = dateSetFormat($year . '/' . $month . '/' . $day, 0, 1);
        // $vertaDate = verta($date);
        // // dd($vertaDate);
        // $date = [
        //     $date->year, $date->month, $date->day
        // ];

        //  dd($request->all(),$date, Verta::jalaliToGregorian($year, $month, $day));
        // $total_payments = Payment::where('price', '>', 0)->whereYear('created_at', $date[0])
        //     ->whereMonth('created_at', $date[1])
        //     ->whereDay('created_at', $date[2])
        //     ->get()->sum('price');
        // $total_offered = Payment::where('type', 'offer')->whereYear('created_at', $date[0])
        //     ->whereMonth('created_at', $date[1])
        //     ->whereDay('created_at', $date[2])
        //     ->sum('price');
        // $total_offered *= -1;
        // $total_rounded = Payment::where('type', 'rounded')->whereYear('created_at', $date[0])
        //     ->whereMonth('created_at', $date[1])
        //     ->whereDay('created_at', $date[2])
        //     ->sum('price');
        // $total_rounded *= -1;
        // $total_vat = Payment::where('type', 'vat')->whereYear('created_at', $date[0])
        //     ->whereMonth('created_at', $date[1])
        //     ->whereDay('created_at', $date[2])
        //     ->sum('price');
        // $total_vat *= -1;
        // $total_factors = Factor::whereYear('created_at', $date[0])
        //     ->whereMonth('created_at', $date[1])
        //     ->whereDay('created_at', $date[2])
        //     ->get()->sum('final_price');
        // $total_games = Game::whereYear('created_at', $date[0])
        //     ->whereMonth('created_at', $date[1])
        //     ->whereDay('created_at', $date[2])
        //     ->get()->sum('game_price');
        // $total_costs = Cost::whereYear('created_at', $date[0])
        //     ->whereMonth('created_at', $date[1])
        //     ->whereDay('created_at', $date[2])
        //     ->get()->sum('price');
        $paymentType = new PaymentType();
        $gameController= new GameController();
        $searchData=['from_date' => $fromDate, 'to_date' => $toDate];
        $paymentTypeTable = $paymentType->showPerPaymentType($searchData);
        $section_table = $gameController->sumPerSectionView($searchData);
        $costTable=CostCategory::costTableReport($searchData);
        //  dd($paymentTypeTable->render());
        return [
            // 'total_payments' => cnf($total_vat + $total_rounded + $total_offered + $total_games + $total_factors),
            // 'total_factors' => cnf($total_factors),
            // 'total_games' => cnf($total_games),
            // 'total_offered' => cnf($total_offered),
            // 'total_rounded' => cnf($total_rounded),
            // 'total_vat' => cnf($total_vat),
            // 'total_costs' => cnf($total_costs),
            'payment_type_view' => $paymentTypeTable->render(),
            'section_table' => $section_table->render(),
            'costTable' => $costTable->render()
        ];
    }

    public function balance()
    {
        return view('report.balance');
    }


    public function getSum(Request $request)
    {
        $payment = new PaymentController;
        $game = new GameController;
        $factor = new FactorController;
        $cost = new  CostController;
        $paymentType = new PaymentType;
        $g_sum = $game->getSum($request, false);
        $f_sum = $factor->getSum($request);
        $c_sum = $cost->getSum($request);
        $request->merge(['from_price' => '0']);
        $offered_price = $payment->getSum($request, 'offered');
        $rounded = $payment->getSum($request, 'rounded');
        $vat = $payment->getSum($request, 'vat');
        $request->merge(['from_price' => '', 'to_price' => '0', 'withOffers' => true]);
        $p_sum = abs($payment->getSum($request));
        $sum = $p_sum - $c_sum;
        $positive_p_sum = $f_sum + $g_sum + (-1 * $vat) + (-1 * $rounded) + (-1 * $offered_price);
        //tables
        $searchData=['from_date' => Verta::today(), 'to_date' => Verta::today()];
        $costTable=CostCategory::costTableReport($searchData);
        $paymentTypeTable = $paymentType->showPerPaymentType($searchData);
        $sectionTable=$game->sumPerSectionView($searchData);
        return [
            'payment' => cnf($p_sum),
            'p_payment' => cnf($positive_p_sum),
            'game' => cnf($g_sum),
            'factor' => cnf($f_sum),
            'cost' => cnf($c_sum),
            'sum' => cnf($sum),
            'monthes' => $this->getMonthes(),
            'years' => $this->getYears(),
            'offered' => cnf(-1 * $offered_price),
            'rounded' => cnf(-1 * $rounded),
            'vat' => cnf(-1 * $vat),
            'paymentTypeTable' => $paymentTypeTable,
            'sectionTable'=>$sectionTable,
            'costTable'=>$costTable
        ];
    }

    public function getMonthes()
    {
        if (app()->getLocale() == 'fa') {
            return [
                1 => 'فروردین',
                2 => 'اردیبهشت',
                3 => 'خرداد',
                4 => 'تیر',
                5 => 'مرداد',
                6 => 'شهریور',
                7 => 'مهر',
                8 => 'آبان',
                9 => 'آذر',
                10 => 'دی',
                11 => 'بهمن',
                12 => 'اسفند'
            ];
        } else {
            return  [
                1 => 'January',
                2 => 'February',
                3 => 'March',
                4 => 'April',
                5 => 'May',
                6 => 'June',
                7 => 'July',
                8 => 'August',
                9 => 'September',
                10 => 'October',
                11 => 'November',
                12 => 'December'
            ];
        }
    }
    public function getYears()
    {
        if (app()->getLocale() == 'fa') {
            $origin = 1400;
            $curentYear = verta()->year;
        } else {
            $origin = 2020;
            $curentYear = now()->year;
        }
        return range($origin, $curentYear);
    }
}
