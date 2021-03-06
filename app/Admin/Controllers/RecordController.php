<?php

namespace App\Admin\Controllers;

use App\Admin\Actions\Record\QuitButton;
use App\Admin\Traits\PermissionCheck;
use App\Exports\RecordExport;
use App\Models\Category;
use App\Models\Company;
use App\Models\Record;
use App\Models\Room;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Widgets\Table;

class RecordController extends AdminController
{
    use PermissionCheck;
    protected $permission = 'records';
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
        $grid->model()->with(['category', 'company', 'room'])->orderBy('id', 'desc');

        $grid->column('category.title', '所属类型');
        $grid->column('company.company_name', '公司当前名称');
        $grid->column('company_name', '入住时公司名称');
        $grid->column('room.title', '房间号');
        $grid->column('gender', '性别');
        $grid->column('deposit_money', '押金');
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
        $grid->column('quitted_at', '退房时间');

        $grid->expandFilter();
        $grid->filter(function ($filter) {
            $filter->disableIdFilter();
            $filter->where(function ($query) {
                if (strpos($this->input, '-') !== false) { // 输入的是楼号、房间号
                    $query->whereHas('room', function ($q) {
                        $q->where('title', 'like', "{$this->input}%");
                    });
                } else { // 输入的是公司名
                    $query->whereHas('company', function ($q) {
                        $q->where('company_name', 'like', "%{$this->input}%");
                    })
                        ->orWhere('company_name', 'like', "%{$this->input}%");
                }
            }, '公司名/房间号');
            $filter->where(function ($query) {
                if ($this->input === 'living') {
                    $query->where('is_living', true);
                }
                if ($this->input === 'quitted') {
                    $query->where('is_living', false);
                }
            }, '状态')->radio([
                'living' => '在住&nbsp;&nbsp;&nbsp;',
                'quitted' => '已退房&nbsp;&nbsp;&nbsp;',
            ]);
        });

        $grid->disableCreateButton();
        $grid->disableRowSelector();
        $grid->actions(function ($actions) {
            if (Admin::user()->can('records.quit')) {
                $actions->add(new QuitButton);
            }
            // 不退房时，只能在居住页面修改，此处不能修改
            if (!$actions->row->quitted_at || !Admin::user()->can('records.edit')) {
                $actions->disableEdit();
            }
            $actions->disableView();
        });

        $grid->exporter(new RecordExport);

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
            ->default('男')
            ->rules('required', [
                'required' => '必须选择',
            ]);
        $form->decimal('deposit_money', '押金')->rules('required', ['required' => '必须填写']);
        $form->decimal('rent', '月租金')->rules('required', ['required' => '必须填写']);
        $form->date('entered_at', '入住时间')
            ->default(date('Y-m-d'))
            ->rules('required', ['required' => '必须填写']);

        $form->number('electric_start_base', '入住时电表底数');
        $form->number('water_start_base', '入住时水表底数');
        $form->date('quitted_at', '退房时间');
        $form->number('electric_end_base', '退房时电表底数');
        $form->number('water_end_base', '退房时水表底数');

        return $form;
    }
}
