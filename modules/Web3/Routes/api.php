<?php

// use Modules\Web3\Http\Controllers\Api\DefaultController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// 前台API路由
Route::group([
    'middleware' => ['auth:sanctum']
], function() {

    // Route::get('v1/default', [DefaultController::class, 'index'])->name('home');
});
