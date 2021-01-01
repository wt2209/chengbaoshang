<?php

namespace App\Admin\Actions\Report;

use App\Imports\DiscountImport;
use Encore\Admin\Actions\Action;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ImportDiscount extends Action
{
    protected $selector = '.import-discount';

    public function handle(Request $request)
    {
        $arr = explode('-', $request->year_month);
        $year = (int) $arr[0];
        $month = (int) $arr[1];
        if ($year < 2000 || $year > 2200 || $month < 1 || $month > 12) {
            return $this->response()->error('错误：月度输入错误');
        }

        $file = $request->file('file');

        // 已经存在discount的记录不会再次导入
        Excel::import(new DiscountImport($year, $month), $file);
        return $this->response()->success('操作成功')->refresh();
    }

    public function html()
    {
        return <<<HTML
        <a class="btn btn-sm btn-warning import-discount" style="margin-left:5px">导入减免</a>
HTML;
    }

    public function form()
    {
        $this->text('year_month', '填写月度')->placeholder('格式：2020-12')->rules('required');
        $this->file('file', '请选择文件')
            ->help('支持xlsx、xls格式。<br>第一行是标题行：公司名、减免额度。<br>第二行是数据行，减免额度必须是大于0小于1的数字，如：0.8、80%，最多支持2位小数。<br>公司名指的是公司当前的全名。')
            ->rules('required');
    }
}
