<?php

namespace App\Services;

use App\Exports\ExcelExport;
use App\Models\ExcelReport;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class Export
{
    public static function export(Collection $collection, array $columns = [])
    {

        if (!($collection->count() > 0)) {
            abort(404, "هیچ داده ای یافت نشد.");
        }
        if (empty($columns)) {
            $data = $collection;
        } else {
          $columns=array_merge(['row','submit_date'],$columns);
            foreach ($columns as $value) {
                $headings[]= __("excel.$value")!="excel.$value"?__("excel.$value"):$value;
            }
            $i=1;
            $data = $collection->map(function($item) use(&$i){
                $item['row']=$i++;
                $item['submit_date']=app()->getLocale()=='fa'?persianTime($item['created_at']):gregorianTime($item['created_at']);
                return $item;
            })->map->only($columns);
        }
        $table = $collection->first()->getTable();

        $headings = empty($headings) ? (new static)->getHeadings($table, $columns) : $headings;

        $excelexport = new ExcelExport($data, $headings);
        $fileName = Verta()->format('YmdHis') . '_' . $table . '.xlsx';
        $path = 'excel-report/' . $fileName;

        Excel::store($excelexport, $path, 'public');
        ExcelReport::create([
            'user_id' => auth()->id(),
            'table' => $table,
            'name' => $fileName,
            'link' => 'storage/' . $path
        ]);
    }

    public function getHeadings(string $table, array $columns = []): array
    {
        $db = new Database();
        $db_name = $db->dbName();
        $headings = [];

        $tableColumnInfos = DB::select('SHOW FULL COLUMNS FROM ' . $table . ' FROM ' . $db_name);
        foreach ($tableColumnInfos as $tableColumnInfo) {
            if (!empty($columns) && !in_array($tableColumnInfo->Field, $columns)) continue;
            $headings[] = $tableColumnInfo->Comment != "" ? $tableColumnInfo->Comment : $tableColumnInfo->Field;
        }

        return $headings;
    }
}
