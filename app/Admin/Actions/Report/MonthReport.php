<?php

namespace App\Admin\Actions\Report;

use App\Exports\MonthReportExport;
use App\Imports\DiscountImport;
use Encore\Admin\Actions\Action;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class MonthReport extends Action
{
    protected $selector = '.month-report';

    public function handle(Request $request)
    {
        $year = (int) $request->year;
        $month = (int) $request->month;
        $filename = "{$year}年{$month}月报表.xlsx";
        (new MonthReportExport($year, $month))->store($filename);
        return $this->response()->success('操作成功')->download($filename);
    }

    public function html()
    {
        return <<<HTML
        <a class="btn btn-sm btn-default month-report" style="margin-left:5px">导出报表</a>
HTML;
    }

    public function form()
    {
        $this->text('year', '填写年度')->placeholder('格式：2020')->rules('required');
        $this->text('month', '填写月度')->placeholder('格式：12')->rules('required');
    }
}
