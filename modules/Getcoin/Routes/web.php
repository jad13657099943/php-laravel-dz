<?php

use Modules\Getcoin\Http\Controllers\GetcoinController;
use Modules\Getcoin\Http\Controllers\admin\QuestWalletController;
use Modules\Getcoin\Http\Controllers\admin\QuestListController;
use Modules\Getcoin\Http\Controllers\admin\QuestDistributeController;

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

// 前台路由
Route::get('/', [GetcoinController::class, 'index'])->name('index');

// 后台路由
Route::group([
    'middleware' => ['auth:admin'],
    'as' => 'admin.',
    'prefix' => 'admin',
    'namespace' => 'Admin',
], function () {

    //任务管理
    Route::group(['prefix' => 'quest_list', 'as' => 'quest_list.'], function () {
        Route::get('/index', [QuestListController::class, 'index'])->name('index');
        Route::get('/create', [QuestListController::class, 'create'])->name('create');
        Route::get('edit_info', [QuestListController::class, 'editInfo'])->name('edit_info');
    });


    //组长任务管理
    Route::group(['prefix' => 'quest_distribute', 'as' => 'quest_distribute.'], function () {
        Route::get('/index', [QuestDistributeController::class, 'index'])->name('index');
        Route::get('/wallet', [QuestDistributeController::class, 'wallet'])->name('wallet');
        Route::get('/bind_wallet', [QuestDistributeController::class, 'bindWallet'])->name('bind_wallet');
    });

    //任务钱包
    Route::group(['prefix' => 'quest_wallet', 'as' => 'quest_wallet.'], function () {
        Route::get('/index', [QuestWalletController::class, 'index'])->name('index');
        Route::get('/create', [QuestWalletController::class, 'create'])->name('create');
    });


});
