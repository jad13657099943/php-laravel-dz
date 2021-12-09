<?php


namespace Modules\Getcoin\Http\Controllers\api;


use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Coin\Models\CoinAsset;
use Modules\Coin\Models\CoinLog;
use Modules\Coin\Models\CoinWithdraw;

class RewardController extends Controller
{

    //统计该币种的奖励情况
    public function total(Request $request)
    {

        $userId = $request->user()->id;
        $symbol = $request->input('symbol');
        //余额  合计奖励  已提奖励
        $balance = CoinAsset::query()->where('user_id', $userId)
                ->where('symbol', $symbol)
                ->value('balance') ?? 0;
        //合计奖励
        $total = CoinLog::query()->where('user_id', $userId)
            ->where('symbol', $symbol)
            ->whereIn('action', ['reward'])
            ->sum('num');
        //已提取奖励
        $withdraw = CoinWithdraw::query()->where('user_id', $userId)
            ->where('symbol', $symbol)
            ->sum('num');

        return [
            'balance' => floatval($balance),
            'total' => floatval($total),
            'withdraw' => floatval($withdraw),
        ];

    }

}
