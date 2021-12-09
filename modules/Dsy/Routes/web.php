<?php

use Modules\Dsy\Http\Controllers\admin\AccountController;
use Modules\Dsy\Http\Controllers\admin\AdminController;
use Modules\Dsy\Http\Controllers\admin\BalanceController;
use Modules\Dsy\Http\Controllers\admin\BannerController;
use Modules\Dsy\Http\Controllers\admin\FilController;
use Modules\Dsy\Http\Controllers\admin\GoodController;
use Modules\Dsy\Http\Controllers\admin\InformationController;
use Modules\Dsy\Http\Controllers\admin\KernelController;
use Modules\Dsy\Http\Controllers\admin\OrderController;
use Modules\Dsy\Http\Controllers\admin\PlayController;
use Modules\Dsy\Http\Controllers\admin\RecordController;
use Modules\Dsy\Http\Controllers\admin\ReleaseController;
use Modules\Dsy\Http\Controllers\admin\TeamController;
use Modules\Dsy\Http\Controllers\admin\TestController;
use Modules\Dsy\Http\Controllers\admin\UniteController;
use Modules\Dsy\Http\Controllers\admin\UserController;
use Modules\Dsy\Http\Controllers\admin\XianController;


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


// 后台路由
Route::group([
    'middleware' => ['auth:admin'],
    'as' => 'admin.',
    'prefix' => 'admin',
    'namespace' => 'Admin',
], function () {
    Route::group(['prefix' => 'good', 'as' => 'good.'], function () {
        Route::get('/index', [GoodController::class, 'index'])->name('index');
        Route::get('/create', [GoodController::class, 'create'])->name('create');
        Route::get('/edit', [GoodController::class, 'edit'])->name('edit');
    });
    Route::group(['prefix' => 'information', 'as' => 'information.'], function () {
        Route::get('/index', [InformationController::class, 'index'])->name('index');
        Route::get('/create', [InformationController::class, 'create'])->name('create');
        Route::get('/edit', [InformationController::class, 'edit'])->name('edit');
    });

    Route::group(['prefix' => 'test', 'as' => 'test.'], function () {
        Route::get('/index', [TestController::class, 'index'])->name('index');
    });
    Route::group(['prefix' => 'user', 'as' => 'user.'], function () {

        Route::get('/tree', [UserController::class, 'tree'])->name('tree');
        Route::get('/index', [UserController::class, 'index'])->name('index');
        Route::get('/edit', [UserController::class, 'edit'])->name('edit');
        Route::get('/edit2', [UserController::class, 'edit2'])->name('edit2');

    });
    Route::group(['prefix' => 'order', 'as' => 'order.'], function () {
        Route::get('/index', [OrderController::class, 'index'])->name('index');
        Route::get('/index2', [OrderController::class, 'index2'])->name('index2');
    });
    Route::group(['prefix' => 'release', 'as' => 'release.'], function () {
        Route::get('/index', [ReleaseController::class, 'index'])->name('index');
    });
    Route::group(['prefix' => 'team', 'as' => 'team.'], function () {
        Route::get('/index', [TeamController::class, 'index'])->name('index');
    });

    Route::group(['prefix' => 'kernel', 'as' => 'kernel.'], function () {

        Route::get('/index', [KernelController::class, 'index'])->name('index');
        Route::get('/index2', [KernelController::class, 'index2'])->name('index2');
        Route::get('/index3', [KernelController::class, 'index3'])->name('index3');
        Route::get('/index4', [KernelController::class, 'index4'])->name('index4');
        Route::get('/index5', [KernelController::class, 'index5'])->name('index5');
        Route::get('/index6', [KernelController::class, 'index6'])->name('index6');
        Route::get('/grade', [KernelController::class, 'grade'])->name('grade');
        Route::get('/order', [KernelController::class, 'order'])->name('order');
        Route::get('/balance', [KernelController::class, 'balance'])->name('balance');
    });

    Route::group(['prefix' => 'banner', 'as' => 'banner.'], function () {
        Route::get('/index', [BannerController::class, 'index'])->name('index');
        Route::get('/create', [BannerController::class, 'create'])->name('create');
        Route::get('/edit', [BannerController::class, 'edit'])->name('edit');
    });
    Route::group(['prefix' => 'fil', 'as' => 'fil.'], function () {
        Route::get('/index', [FilController::class, 'index'])->name('index');
    });
    Route::group(['prefix' => 'admin', 'as' => 'admin.'], function () {
        Route::get('/index', [AdminController::class, 'index'])->name('index');
        Route::get('/add', [AdminController::class, 'add'])->name('add');
        Route::get('/edit', [AdminController::class, 'edit'])->name('edit');
    });

    Route::group(['prefix' => 'account', 'as' => 'account.'], function () {
        Route::get('/index', [AccountController::class, 'index'])->name('index');
        Route::get('/edit', [AccountController::class, 'edit'])->name('edit');
        Route::get('/add', [AccountController::class, 'add'])->name('add');
    });
    Route::group(['prefix' => 'xian', 'as' => 'xian.'], function () {
        Route::get('/index', [XianController::class, 'index'])->name('index');
    });
    Route::group(['prefix' => 'unite', 'as' => 'unite.'], function () {
        Route::get('/index', [UniteController::class, 'index'])->name('index');
        Route::get('/index2', [UniteController::class, 'index2'])->name('index2');
        Route::get('/create', [UniteController::class, 'create'])->name('create');
        Route::get('/edit', [UniteController::class, 'edit'])->name('edit');
    });

    //推荐奖励
    Route::group(['prefix' => 'record', 'as' => 'record.'], function () {

        Route::get('/index', [RecordController::class, 'index'])->name('index');

    });

    //手工打单
    Route::group(['prefix' => 'play', 'as' => 'play.'], function () {
        Route::get('/index', [PlayController::class, 'index'])->name('index');
        Route::get('/order', [PlayController::class, 'order'])->name('order');
        Route::get('/list', [PlayController::class, 'list'])->name('list');
    });

    //资产
    Route::group(['prefix' => 'balance', 'as' => 'balance.'], function () {
        Route::get('/index', [BalanceController::class, 'index'])->name('index');
    });
});
