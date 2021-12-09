<?php

// use Modules\Getcoin\Http\Controllers\Api\DefaultController;
use Modules\Getcoin\Http\Controllers\api\QuestListController;
use Modules\Getcoin\Http\Controllers\api\QuestDistributeController;
use Modules\Getcoin\Http\Controllers\api\UserQuestController;
use Modules\Getcoin\Http\Controllers\api\LeaderQuestController;
use Modules\Getcoin\Http\Controllers\api\LeaderInfoController;
use Modules\Getcoin\Http\Controllers\api\AddressListController;
use Modules\Getcoin\Http\Controllers\api\AccountController;
use Modules\Getcoin\Http\Controllers\api\RewardController;

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
    'middleware' => ['auth:sanctum', 'throttle:60,1'],
], function () {

    //组长看的任务
    Route::group(['prefix' => 'quest', 'as' => 'quest.'], function () {
        Route::get('/list', [QuestListController::class, 'list'])->name('list');
        Route::get('/info', [QuestListController::class, 'info'])->name('info');
        Route::post('/editByLeader', [QuestListController::class, 'editByLeader'])->name('editByLeader');
    });

    //会员看的任务
    Route::group(['prefix' => 'distribute', 'as' => 'distribute.'], function () {
        Route::get('/list', [QuestDistributeController::class, 'list'])->name('list');
        Route::get('/info', [QuestDistributeController::class, 'info'])->name('info');
        //领取任务
        Route::post('/enroll', [QuestDistributeController::class, 'enroll'])->name('enroll');
        //提交任务进度
        Route::post('/submit', [QuestDistributeController::class, 'submitQuest'])->name('submit');
    });


    //会员的任务
    Route::group(['prefix' => 'user_quest', 'as' => 'user_quest.'], function () {
        Route::get('/enroll', [UserQuestController::class, 'userEnrollQuestList'])->name('enroll');
        Route::get('/submit', [UserQuestController::class, 'questSubmitList'])->name('submit');
        Route::get('/reward', [UserQuestController::class, 'questRewardList'])->name('reward');

        Route::get('/followQuestList', [UserQuestController::class, 'followQuestList'])->name('followQuestList');
        Route::post('/followQuest', [UserQuestController::class, 'followQuest'])->name('followQuest');
    });


    //组长的任务
    Route::group(['prefix' => 'leader_distribute', 'as' => 'leader_distribute.'], function () {
        Route::post('/followQuest', [LeaderQuestController::class, 'followQuest'])->name('followQuest');
        Route::get('/distribute', [LeaderQuestController::class, 'distribute'])->name('distribute');
        Route::get('/distributeInfo', [LeaderQuestController::class, 'distributeInfo'])->name('distributeInfo');
        Route::get('/enrollList', [LeaderQuestController::class, 'enrollList'])->name('enrollList');
        Route::get('/submitList', [LeaderQuestController::class, 'submitList'])->name('submitList');
        Route::post('/submitState', [LeaderQuestController::class, 'submitState'])->name('submitState');
    });

    //组长设置
    Route::group(['prefix' => 'leader', 'as' => 'leader_distribute.'], function () {
        Route::get('/info', [LeaderInfoController::class, 'info'])->name('info');
        Route::post('/edit', [LeaderInfoController::class, 'edit'])->name('edit');
    });

    //钱包地址
    Route::group(['prefix' => 'address_list', 'as' => 'address_list.'], function () {
        Route::get('/chain', [AddressListController::class, 'chainList'])->name('chain');
        Route::get('/address', [AddressListController::class, 'chainAddress'])->name('address');
        Route::post('/create', [AddressListController::class, 'createAddress'])->name('create');
        Route::get('/chain_address', [AddressListController::class, 'chainWithOneAddress'])->name('chain_address');
    });


    //绑定第三方地址
    Route::group(['prefix' => 'account', 'as' => 'account.'], function () {
        Route::get('/list', [AccountController::class, 'list'])->name('list');
        Route::get('/info', [AccountController::class, 'info'])->name('info');
        Route::post('/save', [AccountController::class, 'save'])->name('save');
    });

    //奖励部分
    Route::group(['prefix' => 'reward', 'as' => 'reward.'], function () {
        Route::get('/total', [RewardController::class, 'total'])->name('total');
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
