<?php

namespace App\Admin\Controllers;

use App\Admin\Actions\Report\BatchCharge;
use App\Admin\Actions\Report\ChargeButton;
use App\Admin\Actions\Report\DiscountButton;
use App\Admin\Actions\Report\Generate;
use App\Admin\Actions\Report\ImportDiscount;
use App\Admin\Actions\Report\MonthReport;
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
        $grid->column('rent_discount', '减免额度')->display(function ($discount) {
            return ($discount * 100) . '%';
        });
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
                $actions->disableEdit();
            } else if (!$row->charged_at) { // 还没有缴费，此时可以修改
                $actions->add(new ChargeButton);
            } else {
                $actions->disableEdit();
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
            $tools->append(new MonthReport());
        });
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
                    if ($this->input === 'undiscounted') {
                        $query->whereNull('discounted_at');
                    }
                    if ($this->input === 'discounted') {
                        $query->whereNotNull('discounted_at')->whereNull('charged_at');
                    }
                    if ($this->input === 'charged') {
                        $query->whereNotNull('charged_at');
                    }
                }, '状态')->radio([
                    'undiscounted' => '未减免&nbsp;&nbsp;&nbsp;',
                    'discounted' => '已减免&nbsp;&nbsp;&nbsp;',
                    'charged' => '已缴费&nbsp;&nbsp;&nbsp;',
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
        return $grid;
    }

    protected function form()
    {
        $form = new Form(new Report());

        $form->date('start_date', '租期开始日期');
        $form->date('end_date', '租期结束日期');
        $form->number('year', '年');
        $form->number('month', '月');
        $form->number('pre_electric_base', '上期电表数');
        $form->number('current_electric_base', '本期电表数');
        $form->number('electric_amount', '用电量');
        $form->decimal('electric_price', '电单价');
        $form->decimal('electric_money', '电费');
        $form->number('pre_water_base', '上期水表数');
        $form->number('current_water_base', '本期水表数');
        $form->number('water_amount', '用水量');
        $form->decimal('water_price', '水单价');
        $form->decimal('water_money', '水费');
        $form->decimal('rent', '金额');
        $form->decimal('rent_discount', '减免额度');
        $form->decimal('actual_rent', '减免后租金');

        return $form;
    }
}
