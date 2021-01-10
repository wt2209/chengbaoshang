<?php

namespace App\Admin\Actions\Deposit;

use Encore\Admin\Actions\BatchAction;
use Encore\Admin\Auth\Permission;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

class BatchRefund extends BatchAction
{
    public $name = '批量退押金';

    public function handle(Collection $collection, Request $request)
    {
        Permission::check('deposits.refund');

        $refundedAt = $request->refunded_at;
        foreach ($collection as $model) {
            if ($model->charged_at && !$model->refunded_at) {
                $model->refunded_at = $refundedAt;
                $model->refund_company_name = $model->record->company->company_name;
                $model->save();
            }
        }

        return $this->response()->success('操作成功')->refresh();
    }

    public function form()
    {
        $this->date('refunded_at', '退押金时间')->default(now())->rules('required');
    }
}
