<?php

namespace App\Admin\Actions\Report;

use Encore\Admin\Actions\RowAction;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class DiscountButton extends RowAction
{
    public $name = '减免';

    public function handle(Model $model, Request $request)
    {
        $discount = (int) $request->discount;
        if ($discount < 1 || $discount > 100) {
            return $this->response()->error('错误：请输入1-100');
        }
        $model->rent_discount = $discount / 100;
        $model->discounted_at = now();
        $model->actual_rent = round($model->rent * (1 - $model->rent_discount), 2);
        $model->save();

        return $this->response()->success('操作成功')->refresh();
    }

    public function form()
    {
        $this->text('discount', '减免额度')->placeholder('请输入1-100的减免额度')->rules('required');
    }
}
