<?php

use VaahCms\Modules\OrderSystem\Http\Controllers\Backend\ordersController;

Route::group(
    [
        'prefix' => 'backend/ordersystem/orders',
        
        'middleware' => ['web', 'has.backend.access'],
        
],
function () {
    /**
     * Get Assets
     */
    Route::get('/assets', [ordersController::class, 'getAssets'])
        ->name('vh.backend.ordersystem.orders.assets');
    /**
     * Get List
     */
    Route::get('/', [ordersController::class, 'getList'])
        ->name('vh.backend.ordersystem.orders.list');
    /**
     * Update List
     */
    Route::match(['put', 'patch'], '/', [ordersController::class, 'updateList'])
        ->name('vh.backend.ordersystem.orders.list.update');
    /**
     * Delete List
     */
    Route::delete('/', [ordersController::class, 'deleteList'])
        ->name('vh.backend.ordersystem.orders.list.delete');


    /**
     * Fill Form Inputs
     */
    Route::any('/fill', [ordersController::class, 'fillItem'])
        ->name('vh.backend.ordersystem.orders.fill');

    /**
     * Create Item
     */
    Route::post('/', [ordersController::class, 'createItem'])
        ->name('vh.backend.ordersystem.orders.create');
    /**
     * Get Item
     */
    Route::get('/{id}', [ordersController::class, 'getItem'])
        ->name('vh.backend.ordersystem.orders.read');
    /**
     * Update Item
     */
    Route::match(['put', 'patch'], '/{id}', [ordersController::class, 'updateItem'])
        ->name('vh.backend.ordersystem.orders.update');
    /**
     * Delete Item
     */
    Route::delete('/{id}', [ordersController::class, 'deleteItem'])
        ->name('vh.backend.ordersystem.orders.delete');

    /**
     * List Actions
     */
    Route::any('/action/{action}', [ordersController::class, 'listAction'])
        ->name('vh.backend.ordersystem.orders.list.actions');

    /**
     * Item actions
     */
    Route::any('/{id}/action/{action}', [ordersController::class, 'itemAction'])
        ->name('vh.backend.ordersystem.orders.item.action');

    //---------------------------------------------------------

});
