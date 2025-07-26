<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
use App\Http\Controllers\Frontend\FrontendController;
use Illuminate\Support\Facades\Route;

Route::group(
    [
        'prefix'     => 'blogsystem',
        'middleware' => ['web'],
        'namespace' => 'Frontend',
    ],
    function () {
        //------------------------------------------------
        
        Route::get( '/', 'FrontendController@index' )
        ->name( 'vh.frontend.blogsystem' );
        Route::get('/{slug}', 'FrontendController@show')->name('vh.frontend.blogsystem.detail');
        Route::post('/subscribe-newsletter', 'FrontendController@subscribe')->name('newsletter.subscribe');
        
        //------------------------------------------------
    });
