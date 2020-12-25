<?php

namespace App\Admin\Actions;

use Encore\Admin\Actions\BatchAction;
use Illuminate\Database\Eloquent\Collection;

class BatchCharge extends BatchAction
{
    public $name = '批量缴费/扣款';

    public function handle(Collection $collection)
    {
        foreach ($collection as $model) {
            if (!$model->charged_at) {
                $model->charged_at = now();
                $model->save();
            }
        }

        return $this->response()->success('操作成功')->refresh();
    }

    public function dialog()
    {
        $this->confirm('将按今日日期缴费/扣款，确定吗？');
    }

}