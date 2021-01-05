<?php

namespace App\Admin\Actions\Report;

use Encore\Admin\Actions\RowAction;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class ChargeButton extends RowAction
{
    public $name = '缴费/扣款';

    public function handle(Model $model, Request $request)
    {
        // 未减免的不允许缴费
        if (!$model->charged_at && $model->discounted_at) {
            $model->charged_at = $request->charged_at;
            $model->charge_way = $request->charge_way;
            $model->save();
        }

        return $this->response()->success('操作成功')->refresh();
    }

    public function form()
    {
        $this->date('charged_at', '缴费/扣款日期')->default(now())->rules('required');
        $this->radio('charge_way', '缴费/扣款方式')->options([
            '工程款扣款' => '工程款扣款&nbsp;&nbsp;&nbsp;',
            '自行缴费' => '自行缴费&nbsp;&nbsp;&nbsp;',
        ])->default('工程款扣款');
    }
}
