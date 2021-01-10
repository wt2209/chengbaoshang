<?php

namespace App\Exports;

use Encore\Admin\Grid\Exporters\ExcelExporter;
use Maatwebsite\Excel\Concerns\WithMapping;

class RenameExport extends ExcelExporter implements WithMapping
{
    protected $fileName;

    public function __construct()
    {
        $this->fileName = '公司改名明细' . date('Y-m-d') . '.xlsx';
    }

    protected $headings = [
        '现公司名',
        '从',
        '改为',
        '改名日期',
    ];

    public function map($rename): array
    {
        return [
            $rename->company->company_name,
            $rename->old_name,
            $rename->new_name,
            $rename->renamed_at,
        ];
    }
}
