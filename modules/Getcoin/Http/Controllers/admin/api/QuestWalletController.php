<?php


namespace Modules\Getcoin\Http\Controllers\admin\api;


use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Coinv2\Components\TokenIO\TokenIO;
use Modules\Getcoin\Models\QuestWallet;
use Modules\User\Models\ProjectUser;

class QuestWalletController extends Controller
{

    public function index(Request $request)
    {

        $where = [];
        $param = $request->all();
        if ($param['user_id'] ?? false) {
            $where[] = ['user_id', '=', $param['user_id']];
        }
        $result = QuestWallet::query()->where($where)
            ->orderBy('id', 'desc')
            ->paginate($request->limit);

        return $result;
    }

    public function create(Request $request)
    {

        $userId = $request->input('user_id');
        $chain = $request->input('chain');
        $num = $request->input('num');
        $description = $request->input('description');
        if ($num <= 0) {
            throw new \Exception('请输入生成数量');
        }

        $isLeader = ProjectUser::query()->where('user_id', $userId)->value('is_leader');
        if (!$isLeader) {
            throw new \Exception('您不是分发组长，不能生成钱包地址');
        }

        //已生成的数量
        $countNum = QuestWallet::query()->where('user_id', $userId)
            ->where('chain', $chain)
            ->count();

        $arr = [];
        $tokenIO = resolve(TokenIo::class);
        for ($i = 1; $i <= $num; $i++) {

            //account=用户UID，index=已生成数量+(1到：生成数量轮询)
            $account = $userId;
            $index = $countNum + $i;

            $newWallet = $tokenIO->newWallet($chain, $account, $index);
            if ($newWallet['code'] != 0) {
                throw new \Exception('生成钱包地址状态出错');
            }
            $address = $newWallet['data']['address'] ?? null;
            $arr[] = [
                'user_id' => $userId,
                'address' => $address,
                'chain' => $chain,
                'account_index' => $account,
                'index' => $index,
                'gas_balance' => 0,
                'description' => $description,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ];
        }

        if ($arr) {
            \DB::table('quest_wallet')->insert($arr);
        }

        return [
            'msg' => '生成成功',
        ];
    }

}
