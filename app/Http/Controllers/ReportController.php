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

        $paymentType = new PaymentType();
        $gameController= new GameController();
        $searchData=['from_date' => $fromDate, 'to_date' => $toDate];
        $paymentTypeTable = $paymentType->showPerPaymentType($searchData);
        $section_table = $gameController->sumPerSectionView($searchData);
        $costTable=CostCategory::costTableReport($searchData);
        return [
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
