<?php

namespace App\Admin\Actions\Company;

use App\Models\Rename;
use Encore\Admin\Actions\RowAction;
use Encore\Admin\Auth\Permission;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RenameButton extends RowAction
{
    public $name = '改名';

    public function handle(Model $model, Request $request)
    {
        Permission::check('companies.rename');
        // $model ...
        DB::transaction(function () use ($model, $request) {
            $rename = new Rename();
            $rename->company_id = $model->id;
            $rename->new_name = $request->get('new_name');
            $rename->old_name = $model->company_name;
            $rename->renamed_at = $request->get('renamed_at');
            $rename->save();
            $model->company_name = $request->new_name;
            $model->save();
        });

        return $this->response()->success('操作成功')->refresh();
    }

    public function form()
    {
        $this->text('new_name', '新公司名称')->rules('required', [
            'required' => '必须填写',
        ]);
        $this->date('renamed_at', '改名时间')->default(now());
    }
}
