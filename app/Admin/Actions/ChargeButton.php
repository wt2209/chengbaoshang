<?php

namespace App\Admin\Actions;

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
            $model->save();
        }

        return $this->response()->success('操作成功')->refresh();
    }

    public function form()
    {
        $this->date('charged_at', '缴费/扣款日期')->rules('required');
    }
}
