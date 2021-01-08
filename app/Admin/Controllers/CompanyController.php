<?php

namespace App\Admin\Controllers;

use App\Admin\Actions\Company\RenameButton;
use App\Models\Category;
use App\Models\Company;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;

class CompanyController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '公司明细';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $categoryMapper = Category::pluck('title', 'id')->toArray();
        $grid = new Grid(new Company());

        $grid->column('category_id', '所属类型')->display(function ($categoryId) use ($categoryMapper) {
            return $categoryMapper[$categoryId];
        })->filter($categoryMapper);
        $grid->column('company_name', '公司名称');
        $grid->column('business', '业务范围');
        $grid->column('manager', '负责人');
        $grid->column('manager_phone', '负责人电话');
        $grid->column('linkman', '日常联系人');
        $grid->column('linkman_phone', '联系人电话');
        $grid->column('lease', '租期')->display(function () {
            return $this->lease_start ? $this->lease_start . '—' . $this->lease_end : '无';
        });
        $grid->column('remark', '备注');
        $grid->column('created_at', '最早入住公寓时间');

        $grid->quickSearch(function ($model, $query) {
            if (intval($query) > 0) { // 输入的是电话
                $model->where('manager_phone', 'like', "%{$query}%")
                    ->orWhere('linkman_phone', 'like', "%{$query}%");
            } else { // 输入的是公司名或人名
                $model->where('company_name', 'like', "%{$query}%")
                    ->orWhere('manager', 'like', "%{$query}%")
                    ->orWhere('linkman', 'like', "%{$query}%");
            }
        })->placeholder('公司名、人名或电话');

        $grid->disableRowSelector();
        $grid->disableFilter();
        $grid->actions(function ($actions) {
            $actions->add(new RenameButton);
            $actions->disableDelete();
            $actions->disableView();
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
        $form = new Form(new Company());

        // 存在租期的类型id
        $categoryIdsWithLease = Category::where('has_lease', true)->pluck('id')->toArray();
        $form->select('category_id', '所属类型')
            ->options(Category::pluck('title', 'id'))
            ->when($categoryIdsWithLease, function (Form $form) {
                $form->date('lease_start', '租期开始日')
                    ->default(null)
                    ->rules('required', ['required' => '必须填写']);
                $form->date('lease_end', '租期结束日')
                    ->default(null)
                    ->rules('required', ['required' => '必须填写']);
            })
            ->rules('required', [
                'required' => '必须选择',
            ]);
        $form->text('company_name', '公司名称')
            ->creationRules(['required', 'unique:companies'], [
                'required' => '必须填写',
                'unique' => '此公司已存在',
            ])
            ->updateRules(['required', 'unique:companies,company_name,{{id}}'], [
                'required' => '必须填写',
                'unique' => '此公司已存在',
            ]);
        $form->text('manager', '负责人');
        $form->text('business', '业务范围');
        $form->text('manager_phone', '负责人电话');
        $form->text('linkman', '日常联系人');
        $form->text('linkman_phone', '联系人电话');
        $form->textarea('remark', '备注');

        return $form;
    }
}
