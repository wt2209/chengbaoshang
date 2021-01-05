<?php

namespace App\Admin\Actions\Deposit;

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
            if (!$model->charged_at) {
                $model->charged_at = $chargedAt;
                $model->charge_way = '转账';
                $model->save();
            }
        }

        return $this->response()->success('操作成功')->refresh();
    }

    public function form()
    {
        $this->date('charged_at', '缴费/扣款日期')->default(now())->rules('required');
    }
}
