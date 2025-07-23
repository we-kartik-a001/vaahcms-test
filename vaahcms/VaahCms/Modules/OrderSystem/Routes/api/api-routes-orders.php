<?php
use VaahCms\Modules\OrderSystem\Http\Controllers\Backend\ordersController;
/*
 * API url will be: <base-url>/public/api/ordersystem/orders
 */
Route::group(
    [
        'prefix' => 'ordersystem/orders',
        'namespace' => 'Backend',
    ],
function () {

    /**
     * Get Assets
     */
    Route::get('/assets', [ordersController::class, 'getAssets'])
        ->name('vh.backend.ordersystem.api.orders.assets');
    /**
     * Get List
     */
    Route::get('/', [ordersController::class, 'getList'])
        ->name('vh.backend.ordersystem.api.orders.list');
    /**
     * Update List
     */
    Route::match(['put', 'patch'], '/', [ordersController::class, 'updateList'])
        ->name('vh.backend.ordersystem.api.orders.list.update');
    /**
     * Delete List
     */
    Route::delete('/', [ordersController::class, 'deleteList'])
        ->name('vh.backend.ordersystem.api.orders.list.delete');


    /**
     * Create Item
     */
    Route::post('/', [ordersController::class, 'createItem'])
        ->name('vh.backend.ordersystem.api.orders.create');
    /**
     * Get Item
     */
    Route::get('/{id}', [ordersController::class, 'getItem'])
        ->name('vh.backend.ordersystem.api.orders.read');
    /**
     * Update Item
     */
    Route::match(['put', 'patch'], '/{id}', [ordersController::class, 'updateItem'])
        ->name('vh.backend.ordersystem.api.orders.update');
    /**
     * Delete Item
     */
    Route::delete('/{id}', [ordersController::class, 'deleteItem'])
        ->name('vh.backend.ordersystem.api.orders.delete');

    /**
     * List Actions
     */
    Route::any('/action/{action}', [ordersController::class, 'listAction'])
        ->name('vh.backend.ordersystem.api.orders.list.action');

    /**
     * Item actions
     */
    Route::any('/{id}/action/{action}', [ordersController::class, 'itemAction'])
        ->name('vh.backend.ordersystem.api.orders.item.action');



});
