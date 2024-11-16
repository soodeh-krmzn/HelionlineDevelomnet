<?php

namespace App\Http\Controllers;

use App\Models\Admin\ChangeLog;
use App\Models\Game;
use App\Models\Setting;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $game = new Game;
        $columns = json_encode($this->getColumns());
        $headers = $this->getHeaders();
        $logInfo= $this->logInfo();
        return view('dashboard', compact('game', 'columns', 'headers','logInfo'));
    }
    protected function logInfo() {
       return ChangeLog::where('status',1)->latest()->first();
    }
    protected function getColumns()
    {
        $setting = new Setting;
        $columns = explode(',', $setting->getSetting("game_table_columns"));
        $array = [
            ['data' => 'checkbox', 'visible' => false, 'orderable' => false, 'searchable' => false],
            ['data' => 'DT_RowIndex', 'name' => 'DT_RowIndex', 'orderable' => false],
            ['data' => 'person_fullname'],
            ['data' => 'in_time'],
        ];
        foreach ($columns as $column) {
            if ($column != '') {
                array_push($array, ['data' => $column]);
            }
        }
        array_push($array, ['data' => 'action', 'orderable' => false, 'searchable' => false]);
        return $array;
    }

    protected function getHeaders()
    {
        $fa_columns = [
            'person_id' => __('کد عضویت'),
            'count' => __('تعداد'),
            'counter_id' => __('شمارنده'),
            'section_name' => __('بخش'),
            'station_name' => __('ایستگاه'),
            'reg_code'=>__('کد اشتراک')
        ];
        $setting = new Setting;
        $columns = explode(',', $setting->getSetting("game_table_columns"));
        $str = '<th></th>
                <th>' . __('ردیف') . '</th>
                <th>' . __('نام و نام خانوادگی') .'</th>
                <th>' . __('ساعت ورود') . '</th>';
        foreach ($columns as $column) {
            if (array_key_exists($column, $fa_columns)) {
                $str .= '
                <th>' . $fa_columns[$column] . '</th>';
            }
        }
        $str .= '<th>' . __('عملیات') . '</th>';
        return $str;
    }
}
