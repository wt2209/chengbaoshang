<?php

namespace App\Admin\Actions\Bill;

use Encore\Admin\Actions\BatchAction;
use Encore\Admin\Auth\Permission;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

class BatchCharge extends BatchAction
{
    public $name = '批量缴费';

    public function handle(Collection $collection, Request $request)
    {
        Permission::check('bills.charge');

        $way = $request->charge_way;
        $charger = $request->charger;
        $chargedAt = $request->charged_at;
        foreach ($collection as $model) {
            if (!$model->charged_at) {
                $model->charged_at = $chargedAt;
                $model->charge_way = $way;
                $model->charger = $charger;
                $model->save();
            }
        }

        return $this->response()->success('操作成功')->refresh();
    }

    public function form()
    {
        $this->date('charged_at', '缴费日期')->default(now())->rules('required');
        $this->radio('charge_way', '缴费/扣款方式')->options([
            '工程款扣款' => '工程款扣款&nbsp;&nbsp;&nbsp;',
            '自行缴费' => '自行缴费&nbsp;&nbsp;&nbsp;',
        ])->default('自行缴费');
        $this->text('charger', '缴费人');
    }
}
