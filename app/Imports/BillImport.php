<?php

namespace App\Imports;

use App\Models\Bill;
use App\Models\Company;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class BillImport implements ToCollection
{
    /**
     * @param Collection $collection
     */
    public function collection(Collection $collection)
    {
        $data = [];
        $companyMapper = Company::pluck('id', 'company_name');
        foreach ($collection as $row) {
            // 跳过金额不是数字的行（第一行），或公司不存在的行，或类型不存在的行
            if (!is_numeric($row[3]) || !isset($companyMapper[$row[0]]) || empty($row[2])) {
                continue;
            }
            // 存在这个公司
            if (isset($companyMapper[$row[0]])) {
                $data = [
                    'company_id' => $companyMapper[$row[0]],
                    'location' => $row[1],
                    'type' => $row[2],
                    'money' => floatval($row[3]),
                    'description' => $row[4],
                    'remark' => $row[5],
                ];
                $timestamp = $this->excelDateToUnixTimestamp($row[6]);
                if ($timestamp) {
                    $data['charged_at'] = date('Y-m-d', $timestamp);
                    $data['charge_way'] = $row[7];
                    $data['charger'] = $row[8];
                }
                Bill::create($data);
            }
        }
    }

    public function excelDateToUnixTimestamp($excelDate)
    {
        if (!$excelDate) {
            return null;
        }
        if (strpos($excelDate, '-')) {
            return strtotime($excelDate);
        }
        // excel日期类似于： 44199
        return (intval($excelDate)-25569)*86400;
    }
}
