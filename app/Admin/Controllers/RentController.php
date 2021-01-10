<?php

namespace App\Admin\Controllers;

use App\Admin\Actions\Rent\BatchCharge;
use App\Admin\Actions\Rent\ChargeButton;
use App\Admin\Traits\PermissionCheck;
use App\Exports\RentExport;
use App\Models\Rent;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Grid;

class RentController extends AdminController
{
    use PermissionCheck;
    protected $permission = 'rents';
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
        $grid->column('charged_at', '缴费时间');

        $grid->actions(function ($actions) {
            $actions->disableDelete();
            $actions->disableView();
            $row = $actions->row;
            if (!$row->charged_at && Admin::user()->can('rents.charge')) {
                $actions->add(new ChargeButton);
            }
            if (!Admin::user()->can('rents.edit')) {
                $actions->disableEdit();
            }
        });
        $grid->batchActions(function ($batch) {
            $batch->disableDelete();
            if (Admin::user()->can('rents.charge')) {
                $batch->add(new BatchCharge());
            }
        });
        $grid->disableCreateButton();
        $grid->expandFilter();
        $grid->filter(function ($filter) {
            $filter->disableIdFilter();
            $filter->column(1 / 2, function ($filter) {
                $filter->where(function ($query) {
                    $query->whereHas('record.company', function ($query) {
                        $query->where('company_name', 'like', "%{$this->input}%");
                    });
                }, '公司名');
                $filter->where(function ($query) {
                    $arr = explode('-', $this->input);
                    $query->where('year', $arr[0]);
                    if (isset($arr[1])) {
                        $query->where('month', $arr[1]);
                    }
                }, '月度')->placeholder('支持：2020，2020-7');
                $filter->where(function ($query) {
                    if ($this->input === 'uncharged') {
                        $query->whereNull('charged_at');
                    }
                    if ($this->input === 'charged') {
                        $query->whereNotNull('charged_at');
                    }
                }, '状态')->radio([
                    'uncharged' => '未缴费&nbsp;&nbsp;&nbsp;',
                    'charged' => '已缴费&nbsp;&nbsp;&nbsp;',
                ]);
                $filter->in('is_refund', '缴费/退费')->radio([
                    0 => '缴费',
                    1 => '退费',
                ]);
            });
            $filter->column(1 / 2, function ($filter) {
                $filter->where(function ($query) {
                    $query->whereHas('record.room', function ($query) {
                        $query->where('title', 'like', "%{$this->input}%");
                    });
                }, '房间号');
                $filter->like('company_name', '原公司名');
                $filter->between('charged_at', '缴费时间')->date();
            });
        });

        $grid->exporter(new RentExport);

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

        $form->number('year', '年度');
        $form->number('month', '月度');
        $form->decimal('money', '金额');
        $form->date('start_date', '开始日期')->default(date('Y-m-d'));
        $form->date('end_date', '结束日期')->default(date('Y-m-d'));

        return $form;
    }
}
