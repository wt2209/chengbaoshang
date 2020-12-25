<?php

namespace App\Admin\Actions\Deposit;

use Encore\Admin\Actions\RowAction;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class RefundButton extends RowAction
{
    public $name = '退押金';

    public function handle(Model $model, Request $request)
    {
        if ($model->charged_at && !$model->refunded_at) {
            $model->refunded_at = $request->refunded_at;
            $model->refund_company_name = $model->record->company->company_name;
            $model->save();
        }

        return $this->response()->success('操作成功')->refresh();
    }

    public function form()
    {
        $this->date('refunded_at', '退押金日期')->rules('required');
    }
}
