<?php

namespace App\Admin\Controllers;

use App\Models\Company;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class CompanyController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Company';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Company());

        $grid->column('id', __('Id'));
        $grid->column('category_id', __('Category id'));
        $grid->column('company_name', __('Company name'));
        $grid->column('manager', __('Manager'));
        $grid->column('manager_phone', __('Manager phone'));
        $grid->column('linkman', __('Linkman'));
        $grid->column('linkman_phone', __('Linkman phone'));
        $grid->column('remark', __('Remark'));
        $grid->column('created_at', __('Created at'));
        $grid->column('updated_at', __('Updated at'));

        return $grid;
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     * @return Show
     */
    protected function detail($id)
    {
        $show = new Show(Company::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('category_id', __('Category id'));
        $show->field('company_name', __('Company name'));
        $show->field('manager', __('Manager'));
        $show->field('manager_phone', __('Manager phone'));
        $show->field('linkman', __('Linkman'));
        $show->field('linkman_phone', __('Linkman phone'));
        $show->field('remark', __('Remark'));
        $show->field('created_at', __('Created at'));
        $show->field('updated_at', __('Updated at'));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Company());

        $form->switch('category_id', __('Category id'));
        $form->text('company_name', __('Company name'));
        $form->text('manager', __('Manager'));
        $form->text('manager_phone', __('Manager phone'));
        $form->text('linkman', __('Linkman'));
        $form->text('linkman_phone', __('Linkman phone'));
        $form->text('remark', __('Remark'));

        return $form;
    }
}
