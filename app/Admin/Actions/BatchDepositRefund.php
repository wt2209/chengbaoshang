<?php

namespace App\Admin\Actions;

use Encore\Admin\Actions\BatchAction;
use Illuminate\Database\Eloquent\Collection;

class BatchDepositRefund extends BatchAction
{
    public $name = '批量退押金';

    public function handle(Collection $collection)
    {
        foreach ($collection as $model) {
            if ($model->charged_at && !$model->refunded_at) {
                $model->refunded_at = now();
                $model->refund_company_name = $model->record->company->company_name;
                $model->save();
            }
        }

        return $this->response()->success('Success message...')->refresh();
    }

    public function dialog()
    {
        $this->confirm('将按今日日期退押金，确定吗？');
    }
}
