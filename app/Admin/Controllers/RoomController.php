<?php

namespace App\Admin\Controllers;

use App\Admin\Actions\DisableButton;
use App\Admin\Actions\EnableButton;
use App\Models\Room;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class RoomController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '房间';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Room());

        $grid->column('area', '所属区域')->filter();
        $grid->column('building', '楼号')->filter();
        $grid->column('unit', '单元');
        $grid->column('title', '房间号');
        $grid->column('default_number', '默认居住人数');
        $grid->column('default_deposit', '默认押金');
        $grid->column('default_rent', '默认租金');
        $grid->column('remark', '备注');
        $grid->column('is_using', '状态')->display(function ($using) {
            return $using
                ? '<span class="label label-success">正常</span>'
                : '<span class="label label-danger">已禁用</span>';
        });

        $grid->quickSearch('title');

        $grid->filter(function ($filter) {
            $filter->disableIdFilter();
            $filter->equal('title', '房间号');
        });

        $grid->actions(function ($actions) {
            $row = $actions->row;
            if ($row->is_using) {
                $actions->add(new DisableButton);
            } else {
                $actions->add(new EnableButton);
            }
            $actions->disableDelete();
            $actions->disableView();
        });

        $grid->disableRowSelector();

        return $grid;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Room());

        $form->text('area', '所属区域')
            ->rules('required', [
                'required' => '必须填写',
            ]);
        $form->text('title', '房间号')
            ->creationRules(['required', 'unique:rooms'], [
                'required' => '必须填写',
                'unique' => '此房间号已存在',
            ])
            ->updateRules(['required', 'unique:rooms,title,{{id}}'], [
                'required' => '必须填写',
                'unique' => '此房间号已存在',
            ]);
        $form->text('building', '楼号')->rules('required', [
            'required' => '必须填写',
        ]);
        $form->text('unit', '单元')->rules('required', [
            'required' => '必须填写',
        ]);
        $form->number('default_number', '默认居住人数')
            ->rules('required', [
                'required' => '必须填写',
            ]);
        $form->decimal('default_deposit', '默认押金')
            ->rules('required', [
                'required' => '必须填写',
            ]);
        $form->decimal('default_rent', '默认租金')
            ->rules('required', [
                'required' => '必须填写',
            ]);
        $form->textarea('remark', '备注');

        return $form;
    }
}
