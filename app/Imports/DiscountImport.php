<?php

namespace App\Imports;

use App\Models\Company;
use App\Models\Report;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class DiscountImport implements ToCollection
{
    protected $year;
    protected $month;

    public function __construct($year, $month)
    {
        $this->year = $year;
        $this->month = $month;
    }
    /**
     * @param Collection $collection
     */
    public function collection(Collection $collection)
    {
        $data = [];
        $companyMapper = Company::pluck('id', 'company_name');
        foreach ($collection as $row) {
            // 跳过减免额度不是数字的行（第一行）
            if (!is_numeric($row[1])) {
                continue;
            }
            $companyId = $companyMapper[$row[0]];
            // 存在这个公司
            if (isset($companyId)) {
                $data[$companyId] = $row[1];
            }
        }

        // 已经存在discount的记录不会再次导入
        // 以当前公司名为准
        $reports = Report::with('record.company')
            ->whereNull('discounted_at')
            ->where('year', $this->year)
            ->where('month', $this->month)
            ->get();

        foreach ($reports as $report ) {
            $id = $report->record->company->id;
            if (isset($data[$id])) {
                $report->rent_discount = $data[$id];
                $report->actual_rent = round($report->rent * (1 - $data[$id]), 2);
                $report->discounted_at = now();
                $report->save();
            }
        }
    }
}
