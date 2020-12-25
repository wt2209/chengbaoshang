<?php

namespace App\Admin\Controllers;

use App\Admin\Actions\BatchCharge;
use App\Admin\Actions\ChargeButton;
use App\Models\Rent;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class RentController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '预交费房租';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Rent());
        $grid->model()->with(['record.company', 'record.room']);

        $grid->column('is_refund', '退费')->display(function ($isRefund) {
            return $isRefund ? '<span style="color:red">退费</span>' : '';
        });
        $grid->column('record_company_name', '公司当前名称')->display(function () {
            return $this->record->company->company_name;
        })->totalRow('合计');
        $grid->column('record_room_title', '房间号')->display(function () {
            return $this->record->room->title;
        });
        $grid->column('company_name', '原公司名称');
        $grid->column('money', '金额')->totalRow();
        $grid->column('year_month', '月度')->display(function () {
            return $this->year . '-' . $this->month;
        });
        $grid->column('start_date', '租金开始日');
        $grid->column('end_date', '租金结束日');
        $grid->column('status', '状态')->display(function () {
            return $this->charged_at 
            ?  '<span class="label label-success">已缴费</span>'
            : '<span class="label label-warning">未缴费</span>';
        });
        $grid->column('charged_at', '缴费/扣款时间');

        $grid->actions(function($actions){
            $actions->disableDelete();
            $actions->disableView();
            $row = $actions->row;
            if (!$row->charged_at) {
                $actions->add(new ChargeButton);
            }
        });
        $grid->batchActions(function ($batch) {
            $batch->disableDelete();
            $batch->add(new BatchCharge());
        });

        return $grid;
    }


    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Rent());

        $form->number('record_id', __('Record id'));
        $form->text('company_name', __('Company name'));
        $form->decimal('money', __('Money'));
        $form->number('year', __('Year'));
        $form->switch('month', __('Month'));
        $form->date('start_date', __('Start date'))->default(date('Y-m-d'));
        $form->date('end_date', __('End date'))->default(date('Y-m-d'));
        $form->date('charged_at', __('Charged at'))->default(date('Y-m-d'));
        $form->switch('is_refund', __('Is refund'));

        return $form;
    }
}
