<?php
use Modules\Getcoin\Http\Controllers\admin\api\QuestWalletController;
use Modules\Getcoin\Http\Controllers\admin\api\QuestListController;
use Modules\Getcoin\Http\Controllers\admin\api\QuestDistributeController;

Route::group(['prefix' => 'quest_list', 'as' => 'quest_list.'], function () {
    Route::get('/index', [QuestListController::class, 'index'])->name('index');
    Route::post('/create', [QuestListController::class, 'create'])->name('create');
    Route::post('edit_info', [QuestListController::class, 'editInfo'])->name('edit_info');
});

//组长任务管理
Route::group(['prefix' => 'quest_distribute', 'as' => 'quest_distribute.'], function () {
    Route::get('/index', [QuestDistributeController::class, 'index'])->name('index');
    Route::get('/wallet', [QuestDistributeController::class, 'wallet'])->name('wallet');
    Route::post('/bind_wallet', [QuestDistributeController::class, 'bindWallet'])->name('bind_wallet');
    Route::post('/delete_wallet', [QuestDistributeController::class, 'delWallet'])->name('delete_wallet');
});

Route::group(['prefix' => 'quest_wallet', 'as' => 'quest_wallet.'], function () {
    Route::get('/index', [QuestWalletController::class, 'index'])->name('index');
    Route::post('/create', [QuestWalletController::class, 'create'])->name('create');
});
