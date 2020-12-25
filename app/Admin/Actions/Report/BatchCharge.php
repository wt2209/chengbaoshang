<?php

namespace App\Admin\Actions\Report;

use Encore\Admin\Actions\BatchAction;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

class BatchCharge extends BatchAction
{
    public $name = '批量缴费/扣款';

    public function handle(Collection $collection, Request $request)
    {
        $way = $request->charge_way;
        $chargedAt = $request->charged_at;
        foreach ($collection as $model) {
            // 未减免的不允许缴费
            if (!$model->charged_at && $model->discounted_at) {
                $model->charged_at = $chargedAt;
                $model->charge_way = $way;
                $model->save();
            }
        }

        return $this->response()->success('操作成功')->refresh();
    }

    public function form()
    {
        $this->date('charged_at', '缴费/扣款日期')->default(now())->rules('required');
        $this->radio('charge_way', '缴费/扣款方式')->options([
            '扣款' => '扣款&nbsp;&nbsp;&nbsp;',
            '转账' => '转账&nbsp;&nbsp;&nbsp;',
            '现金' => '现金&nbsp;&nbsp;&nbsp;',
        ])->default('扣款');
    }
}
