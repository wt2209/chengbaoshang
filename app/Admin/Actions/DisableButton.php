<?php

namespace App\Admin\Actions;

use Encore\Admin\Actions\RowAction;
use Illuminate\Database\Eloquent\Model;

class DisableButton extends RowAction
{
    public $name = '禁用';

    public function handle(Model $model)
    {
        $model->is_using = false;
        $model->save();
        return $this->response()->success('操作成功')->refresh();
    }
}
