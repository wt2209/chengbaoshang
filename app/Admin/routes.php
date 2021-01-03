<?php

use Encore\Admin\Facades\Admin;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Route;

Admin::routes();

Route::group([
    'prefix'        => config('admin.route.prefix'),
    'namespace'     => config('admin.route.namespace'),
    'middleware'    => config('admin.route.middleware'),
    'as'            => config('admin.route.prefix') . '.',
], function (Router $router) {

    $router->get('/livings', 'LivingController@index')->name('livings.index');
    $router->get('/livings/create', 'LivingController@create')->name('livings.create');
    $router->post('/livings/store', 'LivingController@store')->name('livings.store');

    $router->get('/', 'HomeController@index')->name('home');

    $router->resource('categories', CategoryController::class)
        ->only(['index', 'create', 'store', 'update', 'edit']);

    $router->resource('rooms', RoomController::class)
        ->only(['index', 'create', 'store', 'update', 'edit']);

    $router->resource('companies', CompanyController::class)
        ->only(['index', 'create', 'store', 'update', 'edit']);

    $router->resource('renames', RenameController::class)
        ->only(['index']);

    $router->resource('records', RecordController::class)
        ->only(['index', 'update', 'edit']);

    $router->resource('deposits', DepositController::class)
        ->only(['index', 'update', 'edit']);

    $router->resource('rents', RentController::class)
        ->only(['index', 'update', 'edit']);

    $router->resource('reports', ReportController::class)
        ->only(['index', 'edit', 'update']);

    $router->resource('utility-bases', UtilityBaseController::class)
        ->only(['index', 'create', 'store', 'update', 'edit', 'delete']);
        
    $router->resource('bills', BillController::class)
        ->only(['index', 'create', 'store', 'update', 'edit', 'delete']);
});
