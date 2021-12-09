<?php


namespace Modules\Getcoin\Http\Controllers\admin;


use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Coin\Models\Coin;
use Modules\Getcoin\Models\UserLeader;

class QuestWalletController extends Controller
{

    public function index(Request $request)
    {
        $userId = $request->input('user_id');
        return view('getcoin::admin.quest_wallet.index',[
            'user_id'=>$userId
        ]);
    }

    public function create(Request $request)
    {

        $userId = $request->input('user_id');
        $coin = Coin::query()->where('tokenio_version', 2)
            ->groupBy('chain')
            ->pluck('chain');


        //可生成的数量
        $num = UserLeader::query()->where('user_id', $userId)->value('wallet_index') ?? 0;
        return view('getcoin::admin.quest_wallet.create', [
            'num' => $num,
            'coin' => $coin,
            'user_id' => $userId
        ]);
    }

}
