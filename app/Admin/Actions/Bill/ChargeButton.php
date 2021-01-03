<?php

namespace App\Admin\Actions\Bill;

use Encore\Admin\Actions\RowAction;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class ChargeButton extends RowAction
{
    public $name = '缴费';

    public function handle(Model $model, Request $request)
    {
        if (!$model->charged_at) {
            $model->charged_at = $request->charged_at;
            $model->charger = $request->charger;
            $model->save();
        }

        return $this->response()->success('操作成功')->refresh();
    }

    public function form()
    {
        $this->date('charged_at', '缴费日期')->default(now())->rules('required');
        $this->text('charger', '缴费人')->rules('required');
    }
}
