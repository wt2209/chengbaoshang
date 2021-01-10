<?php

namespace App\Admin\Actions\Bill;

use App\Imports\BillImport;
use Encore\Admin\Actions\Action;
use Encore\Admin\Auth\Permission;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ImportBills extends Action
{
    protected $selector = '.import-bills';

    public function handle(Request $request)
    {
        Permission::check('bills.import');

        $file = $request->file('file');

        // 已经存在discount的记录不会再次导入
        Excel::import(new BillImport, $file);
        return $this->response()->success('操作成功')->refresh();
    }

    public function html()
    {
        return <<<HTML
        <a class="btn btn-sm btn-warning import-bills" style="margin-left:5px">导入费用</a>
HTML;
    }

    public function form()
    {
        $this->file('file', '请选择文件')
            ->help('支持xlsx、xls格式。<br>第一行是标题行：
                    <br>公司名、房间/位置、费用类型、金额、费用说明、备注、缴费时间、缴费方式、缴费人。
                    <br>第二行开始是数据行,公司名、费用类型、金额必须填写。若存在缴费时间且是一个日期（格式：2020-12-21），则导入后是已缴费状态，否则是未缴费状态')
            ->rules('required');
    }
}
