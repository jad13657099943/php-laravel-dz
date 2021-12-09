<?php


namespace Modules\Getcoin\Http\Controllers\api;


use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Coin\Models\CoinConfig;
use Modules\Coin\Models\CoinUserWallet;
use Modules\Coinv2\Components\TokenIO\TokenIO;
use Modules\Getcoin\Models\QuestWallet;

class AddressListController extends Controller
{

    //可使用的链列表
    public function chainList(Request $request)
    {

        $chain = CoinConfig::query()->groupBy('chain')
            ->select('chain')
            ->get();
        return $chain;
    }

    //链对应的钱包地址列表
    public function chainAddress(Request $request)
    {

        $chain = $request->input('chain');
        $userId = $request->user()->id;
        $where[] = ['user_id', '=', $userId];
        $where[] = ['chain', '=', $chain];
        $list = QuestWallet::query()->where($where)
            ->orderBy('id', 'desc')
            ->paginate($request->limit);

        return $list;
    }


    //每条主链，只返回一条地址
    public function chainWithOneAddress(Request $request)
    {

        $userId = $request->user()->id;
        $list = QuestWallet::query()->where('user_id', $userId)
            ->select(['id','address','chain','index'])
            ->groupBy('chain')
            ->orderBy('id', 'desc')
            ->get();
        return $list;
    }


    //生成任务钱包
    public function createAddress(Request $request)
    {

        $chain = $request->input('chain');
        $userId = $request->user()->id;
        $description = '';
        $tokenIO = resolve(TokenIo::class);

        //已生成的数量
        $countNum = QuestWallet::query()->where('user_id', $userId)
            ->where('chain', $chain)
            ->count();
        //account=用户UID，index=已生成数量+(1到：生成数量轮询)
        $account = $userId;
        $index = $countNum + 1;
        $newWallet = $tokenIO->newWallet($chain, $account, $index);
        if ($newWallet['code'] != 0) {
            throw new \Exception('生成钱包地址状态出错');
        }
        $address = $newWallet['data']['address'] ?? null;
        $arr = [
            'user_id' => $userId,
            'address' => $address,
            'chain' => $chain,
            'account_index' => $account,
            'index' => $index,
            'gas_balance' => 0,
            'description' => $description,
        ];

        $model = new QuestWallet($arr);
        $model->save();
        return $model;
    }

}
