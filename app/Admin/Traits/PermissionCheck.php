<?php

namespace App\Admin\Traits;

use Encore\Admin\Auth\Permission;
use Encore\Admin\Layout\Content;

trait PermissionCheck
{
    public function index(Content $content)
    {
        Permission::check($this->permission . '.index');
        return parent::index($content);
    }

    public function edit($id, Content $content)
    {
        Permission::check($this->permission . '.edit');
        return parent::edit($id, $content);
    }

    public function create(Content $content)
    {
        Permission::check($this->permission . '.create');
        return parent::create($content);
    }
}
