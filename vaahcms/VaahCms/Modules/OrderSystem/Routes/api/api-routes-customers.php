<?php
use VaahCms\Modules\OrderSystem\Http\Controllers\Backend\customersController;
/*
 * API url will be: <base-url>/public/api/ordersystem/customers
 */
Route::group(
    [
        'prefix' => 'ordersystem/customers',
        'namespace' => 'Backend',
    ],
function () {

    /**
     * Get Assets
     */
    Route::get('/assets', [customersController::class, 'getAssets'])
        ->name('vh.backend.ordersystem.api.customers.assets');
    /**
     * Get List
     */
    Route::get('/', [customersController::class, 'getList'])
        ->name('vh.backend.ordersystem.api.customers.list');
    /**
     * Update List
     */
    Route::match(['put', 'patch'], '/', [customersController::class, 'updateList'])
        ->name('vh.backend.ordersystem.api.customers.list.update');
    /**
     * Delete List
     */
    Route::delete('/', [customersController::class, 'deleteList'])
        ->name('vh.backend.ordersystem.api.customers.list.delete');


    /**
     * Create Item
     */
    Route::post('/', [customersController::class, 'createItem'])
        ->name('vh.backend.ordersystem.api.customers.create');
    /**
     * Get Item
     */
    Route::get('/{id}', [customersController::class, 'getItem'])
        ->name('vh.backend.ordersystem.api.customers.read');
    /**
     * Update Item
     */
    Route::match(['put', 'patch'], '/{id}', [customersController::class, 'updateItem'])
        ->name('vh.backend.ordersystem.api.customers.update');
    /**
     * Delete Item
     */
    Route::delete('/{id}', [customersController::class, 'deleteItem'])
        ->name('vh.backend.ordersystem.api.customers.delete');

    /**
     * List Actions
     */
    Route::any('/action/{action}', [customersController::class, 'listAction'])
        ->name('vh.backend.ordersystem.api.customers.list.action');

    /**
     * Item actions
     */
    Route::any('/{id}/action/{action}', [customersController::class, 'itemAction'])
        ->name('vh.backend.ordersystem.api.customers.item.action');



});
