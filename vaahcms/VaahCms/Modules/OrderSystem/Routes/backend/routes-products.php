<?php

use VaahCms\Modules\OrderSystem\Http\Controllers\Backend\ProductsController;

Route::group(
    [
        'prefix' => 'backend/ordersystem/products',
        
        'middleware' => ['web', 'has.backend.access'],
        
],
function () {
    /**
     * Get Assets
     */
    Route::get('/assets', [ProductsController::class, 'getAssets'])
        ->name('vh.backend.ordersystem.products.assets');
    /**
     * Get List
     */
    Route::get('/', [ProductsController::class, 'getList'])
        ->name('vh.backend.ordersystem.products.list');
    /**
     * Update List
     */
    Route::match(['put', 'patch'], '/', [ProductsController::class, 'updateList'])
        ->name('vh.backend.ordersystem.products.list.update');
    /**
     * Delete List
     */
    Route::delete('/', [ProductsController::class, 'deleteList'])
        ->name('vh.backend.ordersystem.products.list.delete');


    /**
     * Fill Form Inputs
     */
    Route::any('/fill', [ProductsController::class, 'fillItem'])
        ->name('vh.backend.ordersystem.products.fill');

    /**
     * Create Item
     */
    Route::post('/', [ProductsController::class, 'createItem'])
        ->name('vh.backend.ordersystem.products.create');
    /**
     * Get Item
     */
    Route::get('/{id}', [ProductsController::class, 'getItem'])
        ->name('vh.backend.ordersystem.products.read');
    /**
     * Update Item
     */
    Route::match(['put', 'patch'], '/{id}', [ProductsController::class, 'updateItem'])
        ->name('vh.backend.ordersystem.products.update');
    /**
     * Delete Item
     */
    Route::delete('/{id}', [ProductsController::class, 'deleteItem'])
        ->name('vh.backend.ordersystem.products.delete');

    /**
     * List Actions
     */
    Route::any('/action/{action}', [ProductsController::class, 'listAction'])
        ->name('vh.backend.ordersystem.products.list.actions');

    /**
     * Item actions
     */
    Route::any('/{id}/action/{action}', [ProductsController::class, 'itemAction'])
        ->name('vh.backend.ordersystem.products.item.action');

    //---------------------------------------------------------
    /**
     * Image Uplaod
     */
    Route::post('/upload/image', [ProductsController::class, 'imageUpload'])
        ->name('vh.backend.ordersystem.products.item.action');

    Route::patch('/delete-image', [ProductsController::class, 'deleteImage'])
        ->name('vh.backend.ordersystem.products.item.action');

});
