<?php

namespace App\Admin\Actions\Deposit;

use Encore\Admin\Actions\RowAction;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class ChargeButton extends RowAction
{
    public $name = '缴费/扣款';

    public function handle(Model $model, Request $request)
    {
        if (!$model->charged_at) {
            $model->charged_at = $request->charged_at;
            $model->charge_way = $request->charge_way;
            $model->save();
        }

        return $this->response()->success('操作成功')->refresh();
    }

    public function form()
    {
        $this->date('charged_at', '缴费/扣款日期')->rules('required');
        $this->radio('charge_way', '缴费/扣款方式')->options([
            '扣款' => '扣款&nbsp;&nbsp;&nbsp;',
            '转账' => '转账&nbsp;&nbsp;&nbsp;',
            '现金' => '现金&nbsp;&nbsp;&nbsp;',
        ])->default('扣款');
    }
}
