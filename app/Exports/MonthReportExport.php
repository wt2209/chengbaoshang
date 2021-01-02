<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\Exportable;
use App\Exports\Sheets\ReportDetailSheet;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class MonthReportExport implements WithMultipleSheets
{
    use Exportable;

    protected $year;
    protected $month;

    public function __construct($year, $month)
    {
        $this->year = $year;
        $this->month = $month;
    }

    public function sheets(): array
    {
        $sheets = [
            new ReportDetailSheet($this->year, $this->month),
        ];

        return $sheets;
    }
}
