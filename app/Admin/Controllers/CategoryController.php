<?php

namespace App\Admin\Controllers;

use App\Admin\Actions\DisableButton;
use App\Admin\Actions\EnableButton;
use App\Models\Category;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use App\Admin\Traits\PermissionCheck;

class CategoryController extends AdminController
{
    use PermissionCheck;
    protected $permission = 'categories';
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '所属类型';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Category());

        $grid->column('title', '名称');
        $grid->column('has_lease', '存在租期')->display(function ($hasRent) {
            return $hasRent ? '有租期' : '无';
        });
        $grid->column('remark', '备注');
        $grid->column('is_using', '状态')->display(function ($using) {
            return $using
                ? '<span class="label label-success">正常</span>'
                : '<span class="label label-danger">已禁用</span>';
        });
        $grid->column('created_at', '创建时间');

        $grid->disableFilter();

        $grid->disableRowSelector();

        if (!Admin::user()->can('categories.create')) {
            $grid->disableCreateButton();
        }

        $grid->actions(function ($actions) {
            $row = $actions->row;
            $user = Admin::user();
            if ($row->is_using && $user->can('categories.disable')) {
                $actions->add(new DisableButton);
            }
            if (!$row->is_using && $user->can('categories.enable')) {
                $actions->add(new EnableButton);
            }
            if (!$user->can('categories.edit')) {
                $actions->disableEdit();
            }
            $actions->disableDelete();
            $actions->disableView();
        });

        return $grid;
    }

    protected function form()
    {
        $states = [
            'on'  => ['value' => 1, 'text' => '存在', 'color' => 'danger'],
            'off' => ['value' => 0, 'text' => '不存在'],
        ];

        $form = new Form(new Category());

        $form->text('title', '名称')
            ->creationRules(['required', 'unique:categories'], [
                'required' => '必须填写',
                'unique' => '此名称已存在',
            ])
            ->updateRules(['required', 'unique:categories,title,{{id}}'], [
                'required' => '必须填写',
                'unique' => '此名称已存在',
            ]);
        $form->switch('has_lease', '是否存在租期')->states($states);
        $form->textarea('remark', '备注');

        return $form;
    }
}
