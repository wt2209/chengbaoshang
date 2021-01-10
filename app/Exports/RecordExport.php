<?php

namespace App\Exports;

use Encore\Admin\Grid\Exporters\ExcelExporter;
use Maatwebsite\Excel\Concerns\WithMapping;

class RecordExport extends ExcelExporter implements WithMapping
{
    protected $fileName;

    public function __construct()
    {
        $this->fileName = '入住明细' . date('Y-m-d') . '.xlsx';
    }

    protected $headings = [
        '所属类型',
        '公司当前名称',
        '房间号',
        '入住时公司名称',
        '性别',
        '押金金额',
        '月租金',
        '入住时间',
        '租期开始日',
        '租期结束日',
        '当前状态',
        '入住时电表数',
        '入住时水表数',
        '退房时间',
        '退房时电表数',
        '退房时水表数'
    ];

    public function map($record): array
    {
        return [
            $record->category->title,
            $record->company->company_name,
            $record->room->title,
            $record->company_name,
            $record->gender,
            $record->deposit_money,
            $record->rent,
            $record->entered_at,
            $record->has_lease ? $record->lease_start : null,
            $record->has_lease ? $record->lease_end : null,
            $record->is_living ? '在住' : '已退房',
            $record->electric_start_base,
            $record->water_start_base,
            $record->quitted_at,
            $record->electric_end_base,
            $record->water_end_base,
        ];
    }
}
