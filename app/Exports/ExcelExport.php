<?php

namespace App\Exports;

use Illuminate\Database\Eloquent\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ExcelExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    private $collection;
    private $headings;

    public function __construct($collection, $headings)
    {
        $this->collection = $collection;
        $this->headings = $headings;
    }

    public function headings(): array
    {
        return $this->headings;
    }

    public function collection()
    {
        return $this->collection;
    }
}
