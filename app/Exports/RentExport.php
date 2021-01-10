<?php

namespace App\Exports;

use Encore\Admin\Grid\Exporters\ExcelExporter;
use Maatwebsite\Excel\Concerns\WithMapping;

class RentExport extends ExcelExporter implements WithMapping
{
    protected $fileName;

    public function __construct()
    {
        $this->fileName = '预交费房租明细' . date('Y-m-d') . '.xlsx';
    }

    protected $headings = [
        '是否是退费',
        '公司当前名称',
        '房间号',
        '原公司名称',
        '金额',
        '年度',
        '月度',
        '租金开始日',
        '租金结束日',
        '状态',
        '缴费日期',
    ];

    public function map($rent): array
    {
        return [
            $rent->is_refund,
            $rent->company->company_name,
            $rent->room->title,
            $rent->company_name,
            $rent->money,
            $rent->year,
            $rent->month,
            $rent->start_date,
            $rent->end_date,
            $rent->charged_at ? '已缴费' : '未缴费',
            $rent->charged_at,
        ];
    }
}
