<?php

namespace App\Admin\Actions;

use Encore\Admin\Actions\RowAction;
use Illuminate\Database\Eloquent\Model;

class EnableButton extends RowAction
{
    public $name = '启用';

    public function handle(Model $model)
    {
        $model->is_using = true;
        $model->save();
        return $this->response()->success('操作成功')->refresh();
    }
}
