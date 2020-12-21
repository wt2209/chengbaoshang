<?php

namespace App\Admin\Controllers;

use App\Models\Category;
use App\Models\Company;
use App\Models\Record;
use App\Models\Room;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Widgets\Table;

class RecordController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '入住记录';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Record());

        $grid->column('category.title', '所属类型');
        $grid->column('company.company_name', '当前公司名称');
        $grid->column('company_name', '入住时公司名称');
        $grid->column('room.title', '房间号');
        $grid->column('gender', '性别');
        $grid->column('rent', '月租金');
        $grid->column('entered_at', '入住时间');
        $grid->column('has_lease', '租期')->display(function ($hasLease) {
            return $hasLease ? $this->lease_start . '—' . $this->lease_end : '无';
        });

        $grid->column('bases', '水电底数')->expand(function ($model) {
            $bases = [[
                $model->electric_start_base,
                $model->water_start_base,
                $model->electric_end_base,
                $model->water_end_base,
            ]];

            return new Table(['入住时电表数', '入住时水表数', '退房时电表数', '退房时水表数'], $bases);
        });

        $grid->column('is_living', '状态')->display(function ($isLiving) {
            return $isLiving
                ?  '<span class="label label-success">在住</span>'
                : '<span class="label label-danger">已退房</span>';
        });
        $grid->column('quit_at', '退房时间');

        return $grid;
    }


    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Record());
        if ($form->isCreating()) { // 新增记录时，只使用空房间
            // 在住的房间id
            $isLivingRoomIds = Record::where('is_living', true)->pluck('room_id')->toArray();
            // 所有可以用的空房间
            $roomMapper = Room::where('is_using', true)
                ->whereNotIn('id', $isLivingRoomIds)
                ->pluck('title', 'id');
        } else { // isEditing
            $roomMapper = Room::where('is_using', true)->pluck('title', 'id');
        }

        // 存在租期的类型id
        $categoryIdsWithLease = Category::where('has_lease', true)->pluck('id')->toArray();

        $form->select('company_id', '公司名称')
            ->options(Company::pluck('company_name', 'id'))
            ->rules('required', [
                'required' => '必须选择',
            ]);
        $form->select('room_id', '房间号')
            ->options($roomMapper)
            ->rules('required', [
                'required' => '必须选择',
            ]);
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
        $form->radio('gender', '性别')
            ->options(['男' => '男&nbsp;&nbsp;&nbsp;', '女' => '女'])
            ->rules('required', [
                'required' => '必须选择',
            ]);
        // 只在添加时允许有押金
        if ($form->isCreating()) {
            $form->decimal('deposit', '押金')->rules('required', ['required' => '必须填写']);
        }
        $form->decimal('rent', '月租金')->rules('required', ['required' => '必须填写']);
        $form->date('entered_at', '入住时间')
            ->default(date('Y-m-d'))
            ->rules('required', ['required' => '必须填写']);

        $form->number('electric_start_base', '入住时电表底数');
        $form->number('water_start_base', '入住时水表底数');

        return $form;
    }
}
