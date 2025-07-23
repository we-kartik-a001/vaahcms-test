<?php

use VaahCms\Modules\OrderSystem\Http\Controllers\Backend\customersController;

Route::group(
    [
        'prefix' => 'backend/ordersystem/customers',
        
        'middleware' => ['web', 'has.backend.access'],
        
],
function () {
    /**
     * Get Assets
     */
    Route::get('/assets', [customersController::class, 'getAssets'])
        ->name('vh.backend.ordersystem.customers.assets');
    /**
     * Get List
     */
    Route::get('/', [customersController::class, 'getList'])
        ->name('vh.backend.ordersystem.customers.list');
    /**
     * Update List
     */
    Route::match(['put', 'patch'], '/', [customersController::class, 'updateList'])
        ->name('vh.backend.ordersystem.customers.list.update');
    /**
     * Delete List
     */
    Route::delete('/', [customersController::class, 'deleteList'])
        ->name('vh.backend.ordersystem.customers.list.delete');


    /**
     * Fill Form Inputs
     */
    Route::any('/fill', [customersController::class, 'fillItem'])
        ->name('vh.backend.ordersystem.customers.fill');

    /**
     * Create Item
     */
    Route::post('/', [customersController::class, 'createItem'])
        ->name('vh.backend.ordersystem.customers.create');
    /**
     * Get Item
     */
    Route::get('/{id}', [customersController::class, 'getItem'])
        ->name('vh.backend.ordersystem.customers.read');
    /**
     * Update Item
     */
    Route::match(['put', 'patch'], '/{id}', [customersController::class, 'updateItem'])
        ->name('vh.backend.ordersystem.customers.update');
    /**
     * Delete Item
     */
    Route::delete('/{id}', [customersController::class, 'deleteItem'])
        ->name('vh.backend.ordersystem.customers.delete');

    /**
     * List Actions
     */
    Route::any('/action/{action}', [customersController::class, 'listAction'])
        ->name('vh.backend.ordersystem.customers.list.actions');

    /**
     * Item actions
     */
    Route::any('/{id}/action/{action}', [customersController::class, 'itemAction'])
        ->name('vh.backend.ordersystem.customers.item.action');

    //---------------------------------------------------------

});
