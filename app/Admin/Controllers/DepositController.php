<?php

namespace App\Admin\Controllers;

use App\Models\Company;
use App\Models\Deposit;
use App\Models\Record;
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
        $grid->column('company_name', '开单时公司名称');
        $grid->column('billed_at', '开具时间');
        $grid->column('status', '状态')->display(function () {
            if ($this->refunded_at) {
                return '<span class="label label-danger">已退费</span>';
            } elseif ($this->charged_at) {
                return '<span class="label label-success">已缴费</span>';
            } else {
                return '<span class="label label-warning">未缴费</span>';
            }
        });
        $grid->column('money', '押金金额')->totalRow();
        $grid->column('charged_at', '缴费时间');
        $grid->column('refund_company_name', '退费时公司名称');
        $grid->column('refunded_at', '退费时间');

        $grid->disableRowSelector();
        $grid->disableCreateButton();

        $grid->expandFilter();

        $grid->filter(function ($filter) {
            $filter->disableIdFilter();
            $filter->where(function ($query) {
                $companyIds = Company::where('company_name', 'like', "%{$this->input}%")->pluck('id')->toArray();
                $recordIds = Record::whereIn('company_id', $companyIds)->pluck('id');
                $query->whereIn('record_id', $recordIds)
                    ->orWhere('company_name', 'like', "%{$this->input}%")
                    ->orWhere('refund_company_name', 'like', "%{$this->input}%");
            }, '公司名');
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
