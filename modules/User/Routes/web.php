<?php

//use Modules\User\Http\Controllers\UserController;

use Modules\User\Http\Controllers\admin\CateController;
use Modules\User\Http\Controllers\admin\LabelController;
use Modules\User\Http\Controllers\admin\UserController;
use Modules\User\Http\Controllers\admin\TotalController;
use Modules\User\Http\Controllers\admin\SettingController;
use Modules\User\Http\Controllers\admin\AppealController;
use Modules\User\Http\Controllers\admin\ArticleController;

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
Route::get('/', [UserController::class, 'index'])->name('index');

// 后台路由

Route::group([
    'namespace' => 'Admin',
    'prefix' => 'admin',
    'as' => 'admin.',
    'middleware' => [\Modules\Core\Http\Middleware\UseGuard::class . ':admin'],
], function () {

    Route::group(['prefix' => 'user', 'as' => 'user.'], function () {

        Route::get('/parent_all', [UserController::class, 'parentAll'])->name('parent_all');
        Route::get('/index', [UserController::class, 'index'])->name('index');
        Route::get('/edit_user', [UserController::class, 'userEdit'])->name('edit_user');
        Route::get('/tree', [UserController::class, 'tree'])->name('tree');
    });

    Route::group(['prefix' => 'total', 'as' => 'total.'], function () {

        Route::get('/total', [TotalController::class, 'total'])->name('total');
        Route::get('/asset', [TotalController::class, 'asset'])->name('asset');
    });

    Route::group(['prefix' => 'setting', 'as' => 'setting.'], function () {

        Route::get('/index', [SettingController::class, 'index'])->name('index');
        Route::post('/update', [SettingController::class, 'update'])->name('update');
    });

    Route::group(['prefix' => 'appeal', 'as' => 'appeal.'], function () {
        Route::get('/index', [AppealController::class, 'index'])->name('index');
        Route::get('/info', [AppealController::class, 'info'])->name('info');
    });


    Route::group(['prefix' => 'article', 'as' => 'article.'], function () {
        Route::get('/index', [ArticleController::class, 'index'])->name('index');
        Route::get('/edit_info', [ArticleController::class, 'editInfo'])->name('edit_info');
        Route::get('/create', [ArticleController::class, 'create'])->name('create');
    });

    Route::group(['prefix'=>'article_cate','as'=>'article_cate.'],function (){
        Route::get('/index',[CateController::class,'index'])->name('index');
        Route::get('/add',[CateController::class,'add'])->name('add');
    });

    Route::group(['prefix'=>'article_label','as'=>'article_label.'],function (){
        Route::get('/index',[LabelController::class,'index'])->name('index');
        Route::get('/add',[LabelController::class,'add'])->name('add');
    });
});
