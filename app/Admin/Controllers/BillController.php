<?php

namespace App\Admin\Controllers;

use App\Admin\Actions\Bill\BatchCharge;
use App\Admin\Actions\Bill\ChargeButton;
use App\Admin\Actions\Bill\ImportBills;
use App\Models\Bill;
use App\Models\Company;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;

class BillController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '其他费用';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Bill());

        $grid->column('company.company_name', '公司名称')->totalRow('合计');
        $grid->column('location', '房间号/位置');
        $grid->column('description', '费用说明');
        $grid->column('money', '金额')->totalRow();
        $grid->column('status', '状态')->display(function () {
            return $this->charged_at
                ? '<span class="label label-success">已缴费</span>'
                : '<span class="label label-danger">未缴费</span>';
        });
        $grid->column('charged_at', '缴费时间');
        $grid->column('charge_way', '缴费方式');
        $grid->column('charger', '缴费人');
        $grid->column('created_at', '创建时间');
        $grid->column('remark', '备注');

        $grid->expandFilter();
        $grid->filter(function ($filter) {
            $filter->disableIdFilter();
            $filter->column(1 / 2, function ($filter) {
                $filter->where(function ($query) {
                    $companyIds = Company::where('company_name', 'like', "%{$this->input}%")->pluck('id')->toArray();
                    $query->whereIn('company_id', $companyIds);
                }, '公司名');
                $filter->like('type', '费用类型');
            });
            $filter->column(1 / 2, function ($filter) {
                $filter->like('location', '房间/位置');
                $filter->between('charged_at', '缴费时间')->date();
            });
        });

        $grid->actions(function ($actions) {
            $actions->add(new ChargeButton);
            $actions->disableView();
        });
        $grid->batchActions(function ($batch) {
            $batch->add(new BatchCharge());
        });
        $grid->tools(function (Grid\Tools $tools) {
            $tools->append(new ImportBills());
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
        $form = new Form(new Bill());

        if ($form->isCreating()) { // 新增记录时，只使用空房间

        } else { // isEditing
        }

        $form->select('company_id', '公司名称')
            ->options(Company::pluck('company_name', 'id'))
            ->rules('required', [
                'required' => '必须选择',
            ]);
        $form->text('location', '房间号/位置');
        $form->text('type', '费用类型')->rules('required', ['required' => '必须填写']);
        $form->decimal('money', '金额')->rules('required', ['required' => '必须填写']);
        $form->text('description', '费用说明');
        $form->textarea('remark', '备注');
        if ($form->isEditing()) {
            $form->date('charged_at', '缴费时间');
            $form->text('charger', '缴费人');
            $form->text('charge_way', '缴费方式');
        }
     
        return $form;
    }
}
