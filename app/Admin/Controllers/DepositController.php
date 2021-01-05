<?php

namespace App\Admin\Controllers;

use App\Admin\Actions\Deposit\BatchCharge;
use App\Admin\Actions\Deposit\ChargeButton;
use App\Admin\Actions\Deposit\BatchRefund;
use App\Admin\Actions\Deposit\RefundButton;
use App\Models\Deposit;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;

class DepositController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '押金记录';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Deposit());
        $grid->model()->with(['record.company', 'record.room']);

        $grid->column('record_company_name', '公司当前名称')->display(function () {
            return $this->record->company->company_name;
        })->totalRow('合计');
        $grid->column('record_room_title', '房间号')->display(function () {
            return $this->record->room->title;
        });
        $grid->column('record.is_living', '在住')->display(function ($isLiving) {
            return $isLiving
                ? '<span class="label label-success">在住</span>'
                : '<span class="label label-danger">已退房</span>';
        });
        $grid->column('company_name', '入住时公司名称');
        $grid->column('money', '押金金额')->totalRow();
        $grid->column('created_at', '生成时间');
        $grid->column('status', '状态')->display(function () {
            if ($this->refunded_at) {
                return '<span class="label label-danger">已退费</span>';
            } elseif ($this->charged_at) {
                return '<span class="label label-success">已缴费</span>';
            } else {
                return '<span class="label label-warning">未缴费</span>';
            }
        });
        $grid->column('charged_at', '缴费时间');
        $grid->column('charge_way', '缴费方式');
        $grid->column('refund_company_name', '退费时公司名称');
        $grid->column('refunded_at', '退费时间');

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
                $filter->between('charged_at', '缴费时间')->date();
                $filter->where(function ($query) {
                    if ($this->input === 'uncharged') {
                        $query->whereNull('charged_at');
                    }
                    if ($this->input === 'charged') {
                        $query->whereNotNull('charged_at')->whereNull('refunded_at');
                    }
                    if ($this->input === 'refunded') {
                        $query->whereNotNull('refunded_at');
                    }
                }, '状态')->radio([
                    'uncharged' => '未缴费&nbsp;&nbsp;&nbsp;',
                    'charged' => '已缴费&nbsp;&nbsp;&nbsp;',
                    'refunded' => '已退费&nbsp;&nbsp;&nbsp;',
                ]);
            });
            $filter->column(1 / 2, function ($filter) {
                $filter->where(function ($query) {
                    $query->whereHas('record.room', function ($query) {
                        $query->where('title', 'like', "%{$this->input}%");
                    });
                }, '房间号');
                $filter->like('company_name', '入住公司名');
                $filter->like('refund_company_name', '退费公司名');
            });
        });

        $grid->actions(function ($actions) {
            $actions->disableEdit();
            $actions->disableDelete();
            $actions->disableView();
            $row = $actions->row;
            if (!$row->charged_at) {
                $actions->add(new ChargeButton);
            }
            if ($row->charged_at && !$row->refunded_at) {
                $actions->add(new RefundButton);
            }
        });
        $grid->batchActions(function ($batch) {
            $batch->disableDelete();
            $batch->add(new BatchCharge());
            $batch->add(new BatchRefund());
        });
        return $grid;
    }

    /**
     * TODO
     * 暂时禁止改动，看使用情况决定是否允许改动
     */
    protected function form()
    {
        $form = new Form(new Deposit());

        $form->decimal('money', __('Money'));
        $form->date('billed_at', __('Billed at'))->default(date('Y-m-d'));
        $form->date('refunded_at', __('Refunded at'))->default(date('Y-m-d'));
        $form->text('refund_company_name', __('Refund company name'));
        $form->date('charged_at', __('Charged at'))->default(date('Y-m-d'));
        $form->text('company_name', __('Company name'));

        return $form;
    }
}
