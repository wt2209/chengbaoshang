<?php

namespace App\Admin\Controllers;

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

        $grid->column('area', '所属区域');
        $grid->column('title', '房间号');
        $grid->column('building', '楼号');
        $grid->column('unit', '单元');
        $grid->column('default_number', '默认居住人数');
        $grid->column('default_deposit', '默认押金');
        $grid->column('default_rent', '默认租金');
        $grid->column('remark', '备注');
        $grid->column('is_using', __('Is using'));

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
        $show = new Show(Room::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('area', __('Area'));
        $show->field('title', __('Title'));
        $show->field('building', __('Building'));
        $show->field('unit', __('Unit'));
        $show->field('default_number', __('Default number'));
        $show->field('default_deposit', __('Default deposit'));
        $show->field('default_rent', __('Default rent'));
        $show->field('remark', __('Remark'));
        $show->field('is_using', __('Is using'));
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
