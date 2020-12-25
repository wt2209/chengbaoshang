<?php

namespace App\Admin\Actions\Record;

use Encore\Admin\Actions\RowAction;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class QuitButton extends RowAction
{
    public $name = '退房';

    public function form()
    {
        $this->text('electric_end_base', '退房时电表数')->rules('required');
        $this->text('water_end_base', '退房时水表数')->rules('required');
        $this->date('quitted_at', '退房时间')->default(now())->rules('required');
    }

    public function handle(Model $model, Request $request)
    {
        $model->is_living = false;
        $model->quitted_at = $request->quitted_at;
        $model->electric_end_base = $request->electric_end_base;
        $model->water_end_base = $request->water_end_base;
        $model->save();
        return $this->response()->success('操作成功')->refresh();
    }
}
