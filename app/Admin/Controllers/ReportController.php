<?php

namespace App\Admin\Controllers;

use App\Admin\Actions\BatchCharge;
use App\Admin\Actions\ChargeButton;
use App\Admin\Actions\Report\DiscountButton;
use App\Admin\Actions\Report\Generate;
use App\Admin\Actions\Report\ImportDiscount;
use App\Models\Report;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use Encore\Admin\Widgets\Table;

class ReportController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '月度报表';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Report());
        $grid->model()->with(['record.company', 'record.room']);

        $grid->column('record_company_name', '公司当前名称')->display(function () {
            return $this->record->company->company_name;
        })->totalRow('合计');
        $grid->column('record_room_title', '房间号')->display(function () {
            return $this->record->room->title;
        });
        $grid->column('company_name', '报表时公司名称');
        $grid->column('start_date', '租金起止日')->display(function() {
            return $this->start_date . '—' . $this->end_date;
        });
        $grid->column('year_month', '月度')->display(function(){
            return $this->year . '-' . $this->month;
        });
        $grid->column('electric_money', '电费')->totalRow();
        $grid->column('water_money', '水费')->totalRow();
        $grid->column('rent', '租金')->totalRow();
        $grid->column('rent_discount', '减免额度');
        $grid->column('actual_rent', '减免后租金')->totalRow();
        $grid->column('bases', '水电详情')->expand(function ($model) {
            $bases = [[
                $model->pre_electric_base,
                $model->current_electric_base,
                $model->electric_amount,
                $model->electric_price,
                $model->pre_water_base,
                $model->current_water_base,
                $model->water_amount,
                $model->water_price,
            ]];

            return new Table([
                '上期电表数', '本期电表数', '用电量', '电单价',
                '上期水表数', '本期水表数', '用水量', '水单价'
            ], $bases);
        });
        $grid->column('charged_at', '缴费/扣款时间');

        $grid->disableCreateButton();

        $grid->actions(function ($actions) {
            $row = $actions->row;
            if ($row->discounted_at) { // 已经有减免了
                $actions->add(new ChargeButton);
            } else { // 还没有减免
                $actions->add(new DiscountButton);
            }
            $actions->disableDelete();
            $actions->disableView();
        });
        $grid->batchActions(function ($batch) {
            $batch->disableDelete();
            $batch->add(new BatchCharge);
        });
        $grid->tools(function (Grid\Tools $tools) {
            $tools->append(new Generate());
            $tools->append(new ImportDiscount());
        });
        return $grid;
    }
}
