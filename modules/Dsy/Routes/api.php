<?php

// use Modules\Zfil\Http\Controllers\Api\DefaultController;

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

use Modules\Core\Http\Middleware\UseGuard;
use Modules\Dsy\Http\Controllers\dist\BannerController;
use Modules\Dsy\Http\Controllers\dist\CommodityController;
use Modules\Dsy\Http\Controllers\dist\InformationController;
use Modules\Dsy\Http\Controllers\dist\OrderController;

use Modules\Dsy\Http\Controllers\dist\TestController;
use Modules\Dsy\Http\Controllers\dist\UserController;
use Modules\Dsy\Http\Controllers\dist\UserSyController;
use Modules\Dsy\Http\Controllers\dist\ZhiController;
use Modules\Dsy\Http\Controllers\dsy\CurlController;
use Modules\Dsy\Http\Controllers\dsy\IndexController;

use Modules\Dsy\Http\Controllers\dsy\TotalController;
use Modules\Dsy\Http\Controllers\dz\DzController;
use Modules\Dsy\Http\Controllers\dz\NodeController;


Route::group(['middleware' => 'guest'], function () {

    //斑羚
    Route::group(['prefix' => 'banner', 'as' => 'banner.'], function () {
        //banner列表
        Route::get('/list', [BannerController::class, 'banner'])->name('list');
    });

    Route::group(['prefix' => 'user', 'as' => 'user.'], function () {
        //注册
        Route::post('/register', [UserController::class, 'register'])->name('register');
        //登录
        Route::get('/login', [UserController::class, 'login'])->name('login');
        //验证码登录
        Route::get('/logins', [UserController::class, 'codeLogin'])->name('logins');
        //手机验证码
        Route::post('/mobile', [UserController::class, 'getMobile'])->name('mobile');
        //邮箱验证码
        Route::post('/email', [UserController::class, 'getEmail'])->name('email');

    });

    Route::group(['prefix' => 'information', 'as' => 'information.'], function () {
        //资讯列表
        Route::get('/list', [InformationController::class, 'list'])->name('list');
        //资讯详情
        Route::get('/detail', [InformationController::class, 'detail'])->name('detail');
    });

    Route::group(['prefix' => 'commodity', 'as' => 'commodity.'], function () {
        //矿机列表
        Route::get('/list', [CommodityController::class, 'list'])->name('list');
        //矿机详情
        Route::get('/detail', [CommodityController::class, 'detail'])->name('detail');

    });


    Route::group(['prefix' => 'test', 'as' => 'test.'], function () {


        //测试支付
        Route::get('/payOrder', [TestController::class, 'payOrder'])->name('payOrder');

        Route::get('/get', [TestController::class, 'getGrade'])->name('get');
    });


    Route::group(['prefix' => 'sy', 'as' => 'sy.'], function () {
        //释放
        Route::get('/fil', [UserSyController::class, 'Release'])->name('fil');
        //释放
        Route::get('/fil2', [UserSyController::class, 'teamRelease'])->name('fil2');

        //线性释放
        Route::get('/fil5', [UserSyController::class, 'xianRelease'])->name('fil5');

        //基础累计
        Route::get('/total', [TotalController::class, 'total'])->name('total');
        //基础团队
        Route::get('/totals', [TotalController::class, 'totals'])->name('totals');
        //矫正余额
        Route::get('/del', [TotalController::class, 'del'])->name('del');
        //比例计算
        Route::get('/bl', [TotalController::class, 'bl'])->name('bl');
        //计算订单金额
        Route::get('/sum2', [TotalController::class, 'sum'])->name('sum2');
        //计算全球金额
        Route::get('/global', [TotalController::class, 'global'])->name('global');
        //计算全球比例
        Route::get('/globalBl', [TotalController::class, 'globalBl'])->name('globalBl');
        //矫正来源
        Route::get('/users', [TotalController::class, 'users'])->name('users');
    });

    Route::group(['prefix' => 'curl', 'as' => 'curl.'], function () {

        Route::get('/re', [DzController::class, 'recharge'])->name('re');
        //黄小心
        Route::get('/xxx', [CurlController::class, 'xxx'])->name('xxx');

    });

});
Route::group(['middleware' => 'auth:sanctum'], function () {



    Route::group(['prefix' => 'user', 'as' => 'user.'], function () {
        //用户信息
        Route::get('/user', [UserController::class, 'user'])->name('user');
        //修改支付密码
        Route::post('/pay_password', [UserController::class, 'setPatPassword'])->name('pay_password');
        //手机验证码
        Route::post('/mobiles', [UserController::class, 'getMobiles'])->name('mobiles');
    });


    Route::group(['prefix' => 'order', 'as' => 'order.'], function () {
        //立即下单信息
        Route::get('/placeAnOrder', [OrderController::class, 'placeAnOrder'])->name('placeAnOrder');
        //支付
        Route::post('/payOrder', [OrderController::class, 'payOrder'])->name('payOrder');
    });


    Route::group(['prefix' => 'zhi', 'as' => 'zhi.'], function () {
        //我的总质押量
        Route::get('/user', [ZhiController::class, 'user'])->name('user');
        //质押数量
        Route::get('/num', [ZhiController::class, 'num'])->name('num');
        //质押详情
        Route::get('/orderZhi', [ZhiController::class, 'orderZhi'])->name('orderZhi');
        //质押记录列表
        Route::get('/list', [ZhiController::class, 'orderZhiList'])->name('list');
        //质押数据详情
        Route::get('/detail', [ZhiController::class, 'setZhiDetail'])->name('detail');
        //增加质押
        Route::post('/set', [ZhiController::class, 'setZhi'])->name('set');
        //赎回
        Route::post('/redeem', [ZhiController::class, 'redeem'])->name('redeem');
    });


    //鼎盛云
    Route::group(['prefix' => 'dsy', 'as' => 'dsy.'], function () {
        //栏目
        Route::get('/chain', [IndexController::class, 'chainList'])->name('chain');
        //联合产品
        Route::get('/unite', [IndexController::class, 'uniteList'])->name('unite');
        //存储产品
        Route::get('/commodity', [IndexController::class, 'commodityList'])->name('commodity');
        //我的订单
        Route::get('/order', [IndexController::class, 'orderList'])->name('order');
        //我的资产
        Route::get('/balance', [IndexController::class, 'balanceList'])->name('balance');
        //我的收益
        Route::get('/sy', [IndexController::class, 'syList'])->name('sy');
        //我的收支
        Route::get('/sz', [IndexController::class, 'incomeExpensesList'])->name('sz');
        //我的信息
        Route::get('/message', [IndexController::class, 'userMessage'])->name('message');
        //会员等级
        Route::get('/grade', [IndexController::class, 'userGrade'])->name('grade');
        //邀请信息
        Route::get('/invite', [IndexController::class, 'invite'])->name('invite');
        //币种列表
        Route::get('/symbol', [IndexController::class, 'symbolList'])->name('symbol');
        //手续费
        Route::get('/free', [IndexController::class, 'freeList'])->name('free');
    });

    //大正
    Route::group(['prefix' => 'dz', 'as' => 'dz.'], function () {
        //团队信息
        Route::get('/team', [DzController::class, 'team']);
        //团队列表
        Route::get('/teamList', [DzController::class, 'teamList']);
        //节点数据
        Route::get('/node',[NodeController::class,'node']);
        //每日节点数据
        Route::get('/day',[NodeController::class,'dayNode']);
    });


});

Route::group([
    'namespace' => 'Admin\Api',
    'prefix' => 'admin',
    'as' => 'admin.api.',
    'middleware' => [UseGuard::class . ':admin'],
], function () {


    include_route_files(__DIR__ . '/api/admin/');
});
