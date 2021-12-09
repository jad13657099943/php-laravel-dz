<?php

// use Modules\User\Http\Controllers\Api\DefaultController;
use Modules\User\Http\Controllers\api\LoginController;
use Modules\User\Http\Controllers\api\UserController;
use Modules\User\Http\Controllers\api\AppealController;
use Modules\User\Http\Controllers\api\PaymentController;
use Modules\User\Http\Controllers\api\ArticleController;
use Modules\User\Http\Controllers\api\UserNoticeController;

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

Route::group(['middleware' => 'guest'], function () {
    Route::post('/reg', [LoginController::class, 'register'])->name('reg')
        ->middleware('throttle:30,1');
    Route::post('/login', [LoginController::class, 'login'])->name('login')
        ->middleware('throttle:60,1');

    Route::group(['prefix' => 'article', 'as' => 'article.'], function () {
        Route::get('/cate_list', [ArticleController::class, 'cateList'])->name('cate_list');
        Route::get('/list', [ArticleController::class, 'articleList'])->name('list');
        Route::get('/info', [ArticleController::class, 'articleInfo'])->name('info');
    });

});

Route::group([
    'middleware' => ['auth:sanctum']
], function () {

    Route::group(['prefix' => 'user', 'as' => 'user.'], function () {
        Route::get('/info', [UserController::class, 'info'])->name('info');
        Route::post('/edit', [UserController::class, 'editInfo'])->name('edit');
        Route::get('/team', [UserController::class, 'userSons'])->name('team');
        Route::get('/team_total', [UserController::class, 'teamTotal'])->name('team_total');
        Route::get('/coin_balance', [UserController::class, 'coinBalance'])->name('coin_balance');
    });


    //工单申诉
    Route::group(['prefix' => 'appeal', 'as' => 'appeal.'], function () {
        Route::post('/add', [AppealController::class, 'add'])->name('add');
        Route::get('/list', [AppealController::class, 'list'])->name('list');
        Route::get('/info', [AppealController::class, 'info'])->name('info');
        Route::get('/types', [AppealController::class, 'typeList'])->name('types');
    });


    //收款方式
    Route::group(['prefix' => 'payment', 'as' => 'payment.'], function () {
        Route::get('/list', [PaymentController::class, 'list'])->name('list');
        Route::get('/info', [PaymentController::class, 'info'])->name('info');
        Route::post('/save', [PaymentController::class, 'save'])->name('save');
        Route::post('/delete', [PaymentController::class, 'delete'])->name('delete');
    });

    //通知消息
    Route::group(['prefix' => 'user_notice', 'as' => 'user_notice.'], function () {
        Route::get('/state', [UserNoticeController::class, 'state'])->name('state');
        Route::get('/list', [UserNoticeController::class, 'list'])->name('list');
        Route::get('/info', [UserNoticeController::class, 'info'])->name('info');
    });

});


Route::group([
    'namespace' => 'Admin\Api',
    'prefix' => 'admin',
    'as' => 'admin.api.',
    'middleware' => [\Modules\Core\Http\Middleware\UseGuard::class . ':admin'],
], function () {
    include_route_files(__DIR__ . '/admin/');
});
