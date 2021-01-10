<?php

namespace App\Admin\Actions\Deposit;

use Encore\Admin\Actions\RowAction;
use Encore\Admin\Auth\Permission;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class ChargeButton extends RowAction
{
    public $name = '缴费/扣款';

    public function handle(Model $model, Request $request)
    {
        Permission::check('deposits.charge');

        if (!$model->charged_at) {
            $model->charged_at = $request->charged_at;
            $model->charge_way = '转账';
            $model->save();
        }

        return $this->response()->success('操作成功')->refresh();
    }

    public function form()
    {
        $this->date('charged_at', '缴费/扣款日期')->default(now())->rules('required');
    }
}
