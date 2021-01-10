<?php

use Encore\Admin\Auth\Database\Administrator;
use Encore\Admin\Auth\Database\Menu;
use Encore\Admin\Auth\Database\Permission;
use Encore\Admin\Auth\Database\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminTablesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // create a user.
        Administrator::truncate();
        Administrator::create([
            'username' => 'admin',
            'password' => Hash::make('admin'),
            'name'     => '超级管理员',
        ]);

        // create a role.
        Role::truncate();
        Role::create([
            'name' => '超级管理员',
            'slug' => 'administrator',
        ]);
        Role::create([
            'name' => '管理员',
            'slug' => 'manager',
        ]);

        // add role to user.
        Administrator::first()->roles()->save(Role::first());

        //create a permission
        Permission::truncate();
        Permission::insert([
            [
                'name'        => '所有权限',
                'slug'        => '*',
                'http_method' => '',
                'http_path'   => '*',
            ],
            [
                'name'        => '首页',
                'slug'        => 'dashboard',
                'http_method' => 'GET',
                'http_path'   => '/',
            ],
            [
                'name'        => '居住信息',
                'slug'        => 'livings.index',
                'http_method' => 'GET',
                'http_path'   => '/livings',
            ],
            [
                'name'        => '入住',
                'slug'        => 'livings.create',
                'http_method' => '',
                'http_path'   => "",
            ],
            [
                'name'        => '退房',
                'slug'        => 'livings.quit',
                'http_method' => '',
                'http_path'   => "",
            ],
            [
                'name'        => '类型明细',
                'slug'        => 'categories.index',
                'http_method' => 'GET',
                'http_path'   => "/categories",
            ],
            [
                'name'        => '类型添加',
                'slug'        => 'categories.create',
                'http_method' => 'GET',
                'http_path'   => "/categories/create",
            ],
            [
                'name'        => '类型修改',
                'slug'        => 'categories.edit',
                'http_method' => '',
                'http_path'   => "",
            ],
            [
                'name'        => '类型启用',
                'slug'        => 'categories.enable',
                'http_method' => '',
                'http_path'   => "",
            ],
            [
                'name'        => '类型禁用',
                'slug'        => 'categories.disable',
                'http_method' => '',
                'http_path'   => "",
            ],

            [
                'name'        => '房间明细',
                'slug'        => 'rooms.index',
                'http_method' => 'GET',
                'http_path'   => "/rooms",
            ],
            [
                'name'        => '房间添加',
                'slug'        => 'rooms.create',
                'http_method' => 'GET',
                'http_path'   => "/rooms/create",
            ],
            [
                'name'        => '房间修改',
                'slug'        => 'rooms.edit',
                'http_method' => '',
                'http_path'   => "",
            ],
            [
                'name'        => '房间启用',
                'slug'        => 'rooms.enable',
                'http_method' => '',
                'http_path'   => "",
            ],
            [
                'name'        => '房间禁用',
                'slug'        => 'rooms.disable',
                'http_method' => '',
                'http_path'   => "",
            ],

            [
                'name'        => '公司明细',
                'slug'        => 'companies.index',
                'http_method' => 'GET',
                'http_path'   => "/companies",
            ],
            [
                'name'        => '公司添加',
                'slug'        => 'companies.create',
                'http_method' => 'GET',
                'http_path'   => "/companies/create",
            ],
            [
                'name'        => '公司修改',
                'slug'        => 'companies.edit',
                'http_method' => '',
                'http_path'   => "",
            ],
            [
                'name'        => '公司改名',
                'slug'        => 'companies.rename',
                'http_method' => '',
                'http_path'   => '',
            ],

            [
                'name'        => '公司改名明细',
                'slug'        => 'renames.index',
                'http_method' => 'GET',
                'http_path'   => "/renames",
            ],

            [
                'name'        => '入住记录',
                'slug'        => 'records.index',
                'http_method' => 'GET',
                'http_path'   => "/records",
            ],
            [
                'name'        => '入住记录修改',
                'slug'        => 'records.edit',
                'http_method' => '',
                'http_path'   => "",
            ],
            [
                'name'        => '入住记录退房',
                'slug'        => 'records.quit',
                'http_method' => '',
                'http_path'   => "",
            ],

            [
                'name'        => '押金明细',
                'slug'        => 'deposits.index',
                'http_method' => 'GET',
                'http_path'   => "/deposits",
            ],
            [
                'name'        => '押金修改',
                'slug'        => 'deposits.edit',
                'http_method' => '',
                'http_path'   => "",
            ],
            [
                'name'        => '押金缴费',
                'slug'        => 'deposits.charge',
                'http_method' => '',
                'http_path'   => "",
            ],
            [
                'name'        => '押金退费',
                'slug'        => 'deposits.refund',
                'http_method' => '',
                'http_path'   => "",
            ],

            [
                'name'        => '预交费明细',
                'slug'        => 'rents.index',
                'http_method' => 'GET',
                'http_path'   => "/rents",
            ],
            [
                'name'        => '预交费修改',
                'slug'        => 'rents.edit',
                'http_method' => '',
                'http_path'   => "",
            ],

            [
                'name'        => '月报表明细',
                'slug'        => 'reports.index',
                'http_method' => 'GET',
                'http_path'   => "/reports",
            ],
            [
                'name'        => '月报表修改',
                'slug'        => 'reports.edit',
                'http_method' => '',
                'http_path'   => "",
            ],
            [
                'name'        => '月报表生成',
                'slug'        => 'reports.generate',
                'http_method' => '',
                'http_path'   => "",
            ],
            [
                'name'        => '月报表减免',
                'slug'        => 'reports.discount',
                'http_method' => '',
                'http_path'   => "",
            ],
            [
                'name'        => '月报表导入减免',
                'slug'        => 'reports.discount.import',
                'http_method' => '',
                'http_path'   => "",
            ],

            [
                'name'        => '其他费用明细',
                'slug'        => 'bills.index',
                'http_method' => 'GET',
                'http_path'   => "/bills",
            ],
            [
                'name'        => '其他费用添加',
                'slug'        => 'bills.create',
                'http_method' => 'GET',
                'http_path'   => "/bills/create",
            ],
            [
                'name'        => '其他费用修改',
                'slug'        => 'bills.edit',
                'http_method' => '',
                'http_path'   => "",
            ],
            [
                'name'        => '其他费用导入',
                'slug'        => 'bills.import',
                'http_method' => '',
                'http_path'   => "",
            ],
            [
                'name'        => '其他费用缴费',
                'slug'        => 'bills.charge',
                'http_method' => '',
                'http_path'   => "",
            ],
            [
                'name'        => '其他费用删除',
                'slug'        => 'bills.delete',
                'http_method' => 'DELETE',
                'http_path'   => "/bills",
            ],

            [
                'name'        => '水电表明细',
                'slug'        => 'utilityBases.index',
                'http_method' => 'GET',
                'http_path'   => "/utility-bases",
            ],
            [
                'name'        => '水电表添加',
                'slug'        => 'utilityBases.create',
                'http_method' => 'GET',
                'http_path'   => "/utility-bases/create",
            ],
            [
                'name'        => '水电表修改',
                'slug'        => 'utilityBases.edit',
                'http_method' => '',
                'http_path'   => "",
            ],
            [
                'name'        => '水电表导入',
                'slug'        => 'utilityBases.import',
                'http_method' => "",
                'http_path'   => "",
            ],
            [
                'name'        => '水电表删除',
                'slug'        => 'utilityBases.delete',
                'http_method' => "DELETE",
                'http_path'   => "/utility-bases",
            ],

            [
                'name'        => '登录',
                'slug'        => 'auth.login',
                'http_method' => '',
                'http_path'   => "/auth/login\r\n/auth/logout",
            ],
            [
                'name'        => '用户设置',
                'slug'        => 'auth.setting',
                'http_method' => 'GET,PUT',
                'http_path'   => '/auth/setting',
            ],
            [
                'name'        => '权限管理',
                'slug'        => 'auth.management',
                'http_method' => '',
                'http_path'   => "/auth/roles\r\n/auth/permissions\r\n/auth/menu\r\n/auth/logs",
            ],
        ]);

        Role::first()->permissions()->save(Permission::first());

        // add default menus.
        Menu::truncate();
        Menu::insert([
            [
                'parent_id' => 0,
                'order'     => 1,
                'title'     => '居住',
                'icon'      => 'fa-home',
                'uri'       => '/',
            ],
            [
                'parent_id' => 1,
                'order'     => 2,
                'title'     => '居住信息',
                'icon'      => 'fa-circle-o',
                'uri'       => 'livings',
            ],
            [
                'parent_id' => 1,
                'order'     => 3,
                'title'     => '入住',
                'icon'      => 'fa-circle-o',
                'uri'       => '/livings/create',
            ],
            [
                'parent_id' => 1,
                'order'     => 4,
                'title'     => '退房',
                'icon'      => 'fa-circle-o',
                'uri'       => '/livings/quit',
            ],
            [
                'parent_id' => 0,
                'order'     => 5,
                'title'     => '基础',
                'icon'      => 'fa-bar-chart',
                'uri'       => '/',
            ],
            [
                'parent_id' => 0,
                'order'     => 6,
                'title'     => '费用',
                'icon'      => 'fa-rmb',
                'uri'       => '/',
            ],
            [
                'parent_id' => 0,
                'order'     => 7,
                'title'     => '系统设置',
                'icon'      => 'fa-tasks',
                'uri'       => '',
            ],
            [
                'parent_id' => 5,
                'order'     => 8,
                'title'     => '入住记录',
                'icon'      => 'fa-circle-o',
                'uri'       => 'records',
            ],
            [
                'parent_id' => 5,
                'order'     => 9,
                'title'     => '公司',
                'icon'      => 'fa-circle-o',
                'uri'       => 'companies',
            ],
            [
                'parent_id' => 5,
                'order'     => 10,
                'title'     => '类型',
                'icon'      => 'fa-circle-o',
                'uri'       => 'categories',
            ],
            [
                'parent_id' => 5,
                'order'     => 11,
                'title'     => '房间',
                'icon'      => 'fa-circle-o',
                'uri'       => 'rooms',
            ],
            [
                'parent_id' => 5,
                'order'     => 12,
                'title'     => '公司改名记录',
                'icon'      => 'fa-circle-o',
                'uri'       => 'renames',
            ],
            [
                'parent_id' => 6,
                'order'     => 13,
                'title'     => '押金',
                'icon'      => 'fa-circle-o',
                'uri'       => 'deposits',
            ],
            [
                'parent_id' => 6,
                'order'     => 14,
                'title'     => '预交费租金',
                'icon'      => 'fa-circle-o',
                'uri'       => 'rents',
            ],
            [
                'parent_id' => 6,
                'order'     => 15,
                'title'     => '月报表',
                'icon'      => 'fa-circle-o',
                'uri'       => 'reports',
            ],
            [
                'parent_id' => 6,
                'order'     => 16,
                'title'     => '其他费用',
                'icon'      => 'fa-circle-o',
                'uri'       => 'bills',
            ],

            [
                'parent_id' => 7,
                'order'     => 17,
                'title'     => '用户',
                'icon'      => 'fa-users',
                'uri'       => 'auth/users',
            ],
            [
                'parent_id' => 7,
                'order'     => 18,
                'title'     => '角色',
                'icon'      => 'fa-user',
                'uri'       => 'auth/roles',
            ],
            [
                'parent_id' => 7,
                'order'     => 19,
                'title'     => '权限',
                'icon'      => 'fa-ban',
                'uri'       => 'auth/permissions',
            ],
            [
                'parent_id' => 7,
                'order'     => 20,
                'title'     => '菜单',
                'icon'      => 'fa-bars',
                'uri'       => 'auth/menu',
            ],
            [
                'parent_id' => 7,
                'order'     => 21,
                'title'     => '操作记录',
                'icon'      => 'fa-history',
                'uri'       => 'auth/logs',
            ],
        ]);

        // add role to menu.
        Menu::find(2)->roles()->save(Role::first());
    }
}
