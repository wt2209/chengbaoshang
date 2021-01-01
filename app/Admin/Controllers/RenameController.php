<?php

namespace App\Admin\Controllers;

use App\Models\Rename;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Grid;

class RenameController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '公司改名记录';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Rename());

        $grid->column('company.company_name', '现公司名');
        $grid->column('old_name', '从');
        $grid->column('new_name', '改为');
        $grid->column('renamed_at', '改名时间');

        $grid->disableRowSelector();
        $grid->disableCreateButton();
        $grid->disableActions();
        $grid->disableFilter();
        $grid->quickSearch(function ($model, $query) {
            $model->whereHas('company', function ($q) use ($query) {
                $q->where('company_name', 'like', "%{$query}%");
            })
                ->orWhere('new_name', 'like', "%{$query}%")
                ->orWhere('old_name', 'like', "%{$query}%");
        })->placeholder('公司名');

        return $grid;
    }
}
