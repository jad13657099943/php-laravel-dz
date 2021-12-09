<?php

use Modules\User\Http\Controllers\admin\api\CateController;
use Modules\User\Http\Controllers\admin\api\LabelController;
use Modules\User\Http\Controllers\admin\api\TotalController;
use Modules\User\Http\Controllers\admin\api\UserController;
use Modules\User\Http\Controllers\admin\api\AppealController;
use Modules\User\Http\Controllers\admin\api\ArticleController;
use Modules\User\Http\Controllers\admin\api\UploadController;

Route::group(['prefix' => 'upload', 'as' => 'upload.'], function () {
    Route::post('/upload_for_layedit', [UploadController::class, 'uploadForLayEdit'])->name('upload_for_layedit');
});

Route::group(['prefix' => 'user','as'=>'user.',], function () {

    Route::get('index', [UserController::class, 'index'])->name('index');
    Route::post('user_edit', [UserController::class, 'userEdit'])->name('user_edit');
    Route::any('tree', [UserController::class, 'tree'])->name('tree');
});

Route::group(['prefix' => 'total', 'as' => 'total.'], function () {
    Route::post('/asset', [TotalController::class, 'asset'])->name('asset');
});

Route::group(['prefix' => 'appeal','as'=>'appeal.',], function () {

    Route::get('index', [AppealController::class, 'index'])->name('index');
    Route::post('edit_info', [AppealController::class, 'editInfo'])->name('edit_info');
});

Route::group(['prefix' => 'article', 'as' => 'article.'], function () {
    Route::get('/index', [ArticleController::class, 'index'])->name('index');
    Route::post('/edit_info', [ArticleController::class, 'editInfo'])->name('edit_info');
    Route::post('/create', [ArticleController::class, 'create'])->name('create');
    Route::post('/del', [ArticleController::class, 'del'])->name('del');
});

Route::group(['prefix'=>'article_cate','as'=>'article_cate.'],function (){
    Route::get('/index',[CateController::class,'list'])->name('index');
    Route::post('/add',[CateController::class,'add'])->name('add');
});

Route::group(['prefix'=>'article_label','as'=>'article_label.'],function (){
    Route::get('/index',[LabelController::class,'list'])->name('index');
    Route::post('/add',[LabelController::class,'add'])->name('add');
});
