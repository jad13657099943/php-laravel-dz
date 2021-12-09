<?php

use Modules\Dsy\Http\Controllers\admin\api\AccountController;
use Modules\Dsy\Http\Controllers\admin\api\AdminController;
use Modules\Dsy\Http\Controllers\admin\api\BannerController;
use Modules\Dsy\Http\Controllers\admin\api\FilController;
use Modules\Dsy\Http\Controllers\admin\api\GoodController;
use Modules\Dsy\Http\Controllers\admin\api\InformationController;
use Modules\Dsy\Http\Controllers\admin\api\KernelController;
use Modules\Dsy\Http\Controllers\admin\api\OrderController;
use Modules\Dsy\Http\Controllers\admin\api\PlayController;
use Modules\Dsy\Http\Controllers\admin\api\RecordController;
use Modules\Dsy\Http\Controllers\admin\api\ReleaseController;
use Modules\Dsy\Http\Controllers\admin\api\TeamController;
use Modules\Dsy\Http\Controllers\admin\api\TestController;
use Modules\Dsy\Http\Controllers\admin\api\UniteController;
use Modules\Dsy\Http\Controllers\admin\api\UserController;
use Modules\Dsy\Http\Controllers\admin\api\XianController;

Route::group(['prefix' => 'good', 'as' => 'good.'], function () {
    Route::get('/index', [GoodController::class, 'index'])->name('index');
    Route::post('/add', [GoodController::class, 'add'])->name('add');
    Route::post('/del', [GoodController::class, 'del'])->name('del');
    Route::post('/edit', [GoodController::class, 'edit'])->name('edit');
});
Route::group(['prefix' => 'information', 'as' => 'information.'], function () {
    Route::get('/index', [InformationController::class, 'index'])->name('index');
    Route::post('/add', [InformationController::class, 'add'])->name('add');
    Route::post('/del', [InformationController::class, 'del'])->name('del');
    Route::post('/edit', [InformationController::class, 'edit'])->name('edit');
});

Route::group(['prefix'=>'test','as'=>'test.'],function (){
    Route::post('/edit', [TestController::class, 'edit'])->name('edit');
});

Route::group(['prefix' => 'user', 'as' => 'user.'], function () {
    Route::any('tree', [UserController::class, 'tree'])->name('tree');
    Route::get('/index', [UserController::class, 'index'])->name('index');
    Route::post('/time', [UserController::class, 'setTime'])->name('time');
    Route::post('/save', [UserController::class, 'setSave'])->name('save');
    Route::post('/day', [UserController::class, 'setDaySave'])->name('day');
    Route::post('/sy', [UserController::class, 'setSy'])->name('sy');
    Route::post('/sys', [UserController::class, 'setSys'])->name('sys');
    Route::post('/zhi', [UserController::class, 'setZhi'])->name('zhi');
    Route::post('/del', [UserController::class, 'del'])->name('del');
    Route::post('/password',[UserController::class,'password'])->name('password');
});
Route::group(['prefix'=>'order','as'=>'order.'],function (){
    Route::get('/index',[OrderController::class,'index'])->name('index');
    Route::get('/index2',[OrderController::class,'index2'])->name('index2');
    Route::post('/add',[OrderController::class,'add'])->name('add');
    Route::post('/del',[OrderController::class,'del'])->name('del');
    Route::post('/time',[OrderController::class,'time'])->name('time');
});
Route::group(['prefix'=>'release','as'=>'release.'],function (){
    Route::post('/edit',[ReleaseController::class,'edit'])->name('edit');
});
Route::group(['prefix'=>'team','as'=>'team.'],function (){
    Route::post('/update',[TeamController::class,'update'])->name('update');
    Route::get('/index',[TeamController::class,'index'])->name('index');
});

Route::group(['prefix'=>'kernel','as'=>'kernel.'],function (){
    Route::post('/update',[KernelController::class,'update'])->name('update');
    Route::post('/add',[KernelController::class,'add'])->name('add');
    Route::get('/index',[KernelController::class,'index'])->name('index');
    Route::post('/update2',[KernelController::class,'update2'])->name('update2');
    Route::get('/index2',[KernelController::class,'index2'])->name('index2');
    Route::post('/update3',[KernelController::class,'update3'])->name('update3');
    Route::get('/index3',[KernelController::class,'index3'])->name('index3');
    Route::post('/update4',[KernelController::class,'update4'])->name('update4');
    Route::get('/index4',[KernelController::class,'index4'])->name('index4');
    Route::get('/index5',[KernelController::class,'index5'])->name('index5');
    Route::post('/update6',[KernelController::class,'update6'])->name('update6');
    Route::get('/index6',[KernelController::class,'index6'])->name('index6');
    Route::post('/del',[KernelController::class,'del'])->name('del');
    Route::post('/grade',[KernelController::class,'grade'])->name('grade');
    Route::post('/order',[KernelController::class,'order'])->name('order');
    Route::post('/balance',[KernelController::class,'balance'])->name('balance');
    //分配BZZ
    Route::post('/allot',[KernelController::class,'allot'])->name('allot');
});

Route::group(['prefix'=>'fil','as'=>'fil.'],function (){
    Route::get('/index',[FilController::class,'index'])->name('index');
});
Route::group(['prefix'=>'banner','as'=>'banner.'],function (){
    Route::get('/index',[BannerController::class,'index'])->name('index');
    Route::post('/add',[BannerController::class,'add'])->name('add');
    Route::post('/edit',[BannerController::class,'edit'])->name('edit');
    Route::post('/del', [BannerController::class, 'del'])->name('del');
});

Route::group(['prefix'=>'admin','as'=>'admin.'],function (){
    Route::get('/index',[AdminController::class,'index'])->name('index');
    Route::post('/menus',[AdminController::class,'menus'])->name('menus');
    Route::post('/detail',[AdminController::class,'detail'])->name('detail');
    Route::post('/add',[AdminController::class,'add'])->name('add');
    Route::post('/edit',[AdminController::class,'edit'])->name('edit');
    Route::post('/del',[AdminController::class,'del'])->name('del');
});

Route::group(['prefix'=>'account','as'=>'account.'],function (){
    Route::get('/index',[AccountController::class,'index'])->name('index');
    Route::post('/add',[AccountController::class,'add'])->name('add');
    Route::post('/edit',[AccountController::class,'edit'])->name('edit');
    Route::post('/del',[AccountController::class,'del'])->name('del');
});

Route::group(['prefix'=>'xian','as'=>'xian.'],function (){
    Route::get('/index',[XianController::class,'index'])->name('index');
});
Route::group(['prefix' => 'unite', 'as' => 'unite.'], function () {
    Route::get('/index', [UniteController::class, 'index'])->name('index');
    Route::get('/index2', [UniteController::class, 'index2'])->name('index2');
    Route::post('/update', [UniteController::class, 'update'])->name('update');
    Route::post('/add', [UniteController::class, 'add'])->name('add');
    Route::post('/del', [UniteController::class, 'del'])->name('del');
    Route::post('/edit', [UniteController::class, 'edit'])->name('edit');
    Route::post('/time', [UniteController::class, 'time'])->name('time');
});

//推荐奖励
Route::group(['prefix'=>'record','as'=>'record.'],function (){

    Route::get('/list',[RecordController::class,'list'])->name('list');

});

//手工打单
Route::group(['prefix'=>'play','as'=>'play.'],function (){

    Route::post('/order',[PlayController::class,'order'])->name('order');
    Route::get('/list',[PlayController::class,'list'])->name('list');
    Route::post('/succeed',[PlayController::class,'succeed'])->name('succeed');
});
