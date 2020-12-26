<?php

namespace App\Admin\Controllers;

use App\Admin\Actions\Report\BatchCharge;
use App\Admin\Actions\Report\ChargeButton;
use App\Admin\Actions\Report\DiscountButton;
use App\Admin\Actions\Report\Generate;
use App\Admin\Actions\Report\ImportDiscount;
use App\Models\Report;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
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
        $grid->column('start_date', '租金起止日')->display(function () {
            return $this->start_date . '—' . $this->end_date;
        });
        $grid->column('year_month', '月度')->display(function () {
            return $this->year . '-' . $this->month;
        });
        $grid->column('electric_money', '电费')->totalRow();
        $grid->column('water_money', '水费')->totalRow();
        $grid->column('rent', '租金')->totalRow();
        $grid->column('status', '状态')->display(function () {
            if ($this->charged_at) {
                return '<span class="label label-success">已缴费</span>';
            }
            if ($this->discounted_at) {
                return '<span class="label label-warning">已减免</span>';
            } else {
                return '<span class="label label-danger">未减免</span>';
            }
        });
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
        $grid->column('charge_way', '方式');

        $grid->disableCreateButton();

        $grid->actions(function ($actions) {
            $row = $actions->row;
            if (!$row->discounted_at) { // 还没有减免
                $actions->add(new DiscountButton);
            } else if (!$row->charged_at) { // 还没有缴费
                $actions->add(new ChargeButton);
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

    protected function form()
    {
        $form = new Form(new Report());

        $form->date('start_date', '租期开始日期');
        $form->date('end_date', '租期结束日期');
        $form->number('year', '年');
        $form->number('month', '月');
        $form->decimal('money', __('Money'));   
        $form->date('start_date', __('Start date'))->default(date('Y-m-d'));
        $form->date('end_date', __('End date'))->default(date('Y-m-d'));
        $form->date('charged_at', __('Charged at'))->default(date('Y-m-d'));
        $form->switch('is_refund', __('Is refund'));

        return $form;
    }
}
