<?php

namespace App\Exports;

use Encore\Admin\Grid\Exporters\ExcelExporter;
use Maatwebsite\Excel\Concerns\WithMapping;

class DepositExport extends ExcelExporter implements WithMapping
{
    protected $fileName;

    public function __construct()
    {
        $this->fileName = '押金明细' . date('Y-m-d') . '.xlsx';
    }

    protected $headings = [
        '公司当前名称',
        '房间号',
        '在住',
        '入住时公司名称',
        '押金金额',
        '生成时间',
        '状态',
        '缴费时间',
        '缴费方式',
        '退费时公司名称',
        '退费时间'
    ];

    public function map($deposit): array
    {
        return [
            $deposit->record->company->company_name,
            $deposit->record->room->title,
            $deposit->record->is_living ? '在住' : '已退房',
            $deposit->company_name,
            $deposit->money,
            substr($deposit->created_at, 0, 10),
            $deposit->refunded_at ? '已退费' : ($deposit->charged_at ? '已缴费' : '未缴费'),
            $deposit->charged_at,
            $deposit->charge_way,
            $deposit->refund_company_name,
            $deposit->refunded_at,
        ];
    }
}
