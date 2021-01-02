<?php

namespace App\Admin\Controllers;

use App\Admin\Actions\UtilityBase\ImportBase;
use App\Models\Room;
use App\Models\UtilityBase;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class UtilityBaseController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '水电表底数';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new UtilityBase());

        $grid->column('room.title', '房间号');
        $grid->column('year_month', '月度')->display(function () {
            return $this->year . '-' . $this->month;
        });
        $grid->column('pre_electric_base', '上期电表数');
        $grid->column('current_electric_base', '本期电表数');
        $grid->column('pre_water_base', '上期水表数');
        $grid->column('current_water_base', '本期水表数');

        $grid->expandFilter();
        $grid->filter(function ($filter) {
            $filter->disableIdFilter();
            $filter->where(function ($query) {
                $roomIds = Room::where('title', 'like', "%{$this->input}%")->pluck('id')->toArray();
                $query->whereIn('room_id', $roomIds);
            }, '房间号');
            $filter->where(function ($query) {
                $arr = explode('-', $this->input);
                $query->where('year', $arr[0]);
                if (isset($arr[1])) {
                    $query->where('month', $arr[1]);
                }
            }, '月度')->placeholder('支持：2020，2020-7');
        });

        $grid->actions(function ($actions) {
            $actions->disableView();
        });

        $grid->tools(function (Grid\Tools $tools) {
            $tools->append(new ImportBase());
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
        $form = new Form(new UtilityBase());

        $form->select('room_id', '房间号')
            ->rules('required', ['required' => '必须填写'])
            ->options(Room::where('is_using', true)->pluck('title', 'id')->toArray());
        $form->number('year', '年度')->rules('required', ['required' => '必须填写']);
        $form->number('month', '月度')->rules('required', ['required' => '必须填写']);
        $form->number('pre_electric_base', '上期电表数')->rules('required', ['required' => '必须填写']);
        $form->number('current_electric_base', '本期电表数')->rules('required', ['required' => '必须填写']);
        $form->number('pre_water_base', '上期水表数')->rules('required', ['required' => '必须填写']);
        $form->number('current_water_base', '本期水表数')->rules('required', ['required' => '必须填写']);

        return $form;
    }
}
