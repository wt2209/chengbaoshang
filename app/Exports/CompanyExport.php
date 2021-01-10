<?php

namespace App\Exports;

use Encore\Admin\Grid\Exporters\ExcelExporter;
use Maatwebsite\Excel\Concerns\WithMapping;

class CompanyExport extends ExcelExporter implements WithMapping
{   
    protected $fileName;

    public function __construct()
    {
        $this->fileName = '公司明细' . date('Y-m-d') . '.xlsx';
    }

    protected $headings = [
        '所属类型',
        '公司名称',
        '业务范围',
        '负责人',
        '负责人电话',
        '日常联系人',
        '联系人电话',
        '租期开始日',
        '租期结束日',
        '备注',
        '最早入住公寓时间'
    ];

    public function map($company): array
    {
        return [
            $company->category->title,
            $company->company_name,
            $company->business,
            $company->manager,
            $company->manager_phone,
            $company->linkman,
            $company->linkman_phone,
            $company->lease_start,
            $company->lease_end,
            $company->remark,
            substr($company->created_at, 0, 10),
        ];
    }
}
