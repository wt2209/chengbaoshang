<?php

namespace App\Admin\Actions\UtilityBase;

use App\Imports\UtilityBaseImport;
use Encore\Admin\Actions\Action;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ImportBase extends Action
{
    protected $selector = '.import-base';

    public function handle(Request $request)
    {
        $arr = explode('-', $request->year_month);
        $year = (int) $arr[0];
        $month = (int) $arr[1];
        if ($year < 2000 || $year > 2200 || $month < 1 || $month > 12) {
            return $this->response()->error('错误：月度输入错误');
        }

        Excel::import(new UtilityBaseImport($year, $month), $request->file('file'));
        return $this->response()->success('操作成功')->refresh();
    }

    public function html()
    {
        return <<<HTML
        <a class="btn btn-sm btn-warning import-base">导入底数</a>
HTML;
    }

    public function form()
    {
        $this->text('year_month', '填写月度')->placeholder('格式：2020-12')->rules('required');
        $this->file('file', '请选择文件')->rules('required');
    }
}
