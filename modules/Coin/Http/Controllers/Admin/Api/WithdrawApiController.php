<?php


namespace Modules\Coin\Http\Controllers\Admin\Api;


use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Coin\Exceptions\AdminCommonException;
use Modules\Coin\Models\CoinConfig;
use Modules\Coin\Models\CoinTrade;
use Modules\Coin\Services\BalanceChangeService;
use Modules\Coin\Services\CoinService;
use Modules\Coin\Services\RechargeService;
use Modules\Coin\Services\SystemWalletService;
use Modules\Coin\Services\TradeService;
use Modules\Coin\Services\WithdrawService;
use Modules\Core\Services\Frontend\UserService;
use DB;
use Modules\Core\Translate\TranslateExpression;

class WithdrawApiController extends Controller
{

    /**
     * 提现列表
     * @param Request $request
     * @param WithdrawService $withdrawService
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function index(Request $request, WithdrawService $withdrawService)
    {

        //查询参数
        $where = [];
        $param = $request->input('key');
        if (!empty($param['times'])) {
            $time = explode('||', $param['times']);
            $time[0] = date('Y-m-d H:i:s', strtotime($time[0]));
            $time[1] = date('Y-m-d H:i:s', strtotime($time[1]) + 86400);
            $where[] = ['created_at', '>=', [$time[0]]];
            $where[] = ['created_at', '<=', [$time[1]]];
        }
        if (!empty($param['symbol'])) {
            $where[] = ['symbol', '=', $param['symbol']];
        }
        if (!empty($param['from'])) {
            $where[] = ['from', '=', $param['from']];
        }
        if (!empty($param['id'])) {
            $where[] = ['id', '=', $param['id']];
        }
        if (isset($param['state']) && $param['state'] < 1000) {
            $where[] = ['state', '=', $param['state']];
        }
        if (!empty($param['user_info'])) {

            $userService = resolve(UserService::class);
            $userInfo = $userService->one(['id' => $param['user_info']], [
                'exception' => false,
                'queryCallback' => function ($query) use ($param) {
                    $query->orWhere('username', $param['user_info'])->value('id');
                }
            ]);

            if (!$userInfo) {
                $userId = -1;
            } else {
                $userId = $userInfo->id;
            }
            $where[] = ['user_id', '=', $userId];
        }


        $list = $withdrawService->paginate($where, [
            'with' => ['user', 'coin'],
            'orderBy' => ['id', 'desc'],
        ]);

        $coinService = resolve(CoinService::class);
        foreach ($list as $item) {
            $item->username = $item->user->username;
            $item->num_text = $item->num . $item->symbol;
            if ($item->symbol != 'CNY') {
                $item->to_text = $item->to;//substr($item->to, 0, 4) . '****' . substr($item->to, -4, 4);

                //返回钱包地址跳转到区块链浏览器查询网址
                $item->to_link = $coinService->queryChainLink($item->coin->chain, $item->to);
            } else {

                //解析出银行卡提现信息
                /*$to_text = json_decode($item->to, true);
                $to_text['value'] = json_decode($to_text['value'], true);
                if ($to_text['bank'] == 'bank') { //银行卡

                    $to_data = $to_text['value']['true_name'] . '-' . $to_text['value']['bank_name'] . $to_text['value']['bank_address'] . $to_text['value']['account'];
                } else {
                    //该类型提现数据暂未解析
                    $to_data = "该类型提现数据暂未解析";
                }*/
                $item->to_text = $item->to;
            }
            $item->state_text = $item->state_text;
            //减少数据
            $item->pay_num = $item->num+$item->cost;
        }
        return $list;
    }


    /**
     * 手动更新CNY币种提现状态为已打款
     * @return array
     * @throws CurrencyErrorException
     * @throws ModelSaveFailedException
     */
    public function editState(Request $request, WithdrawService $withdrawService)
    {

        $id = $request->input('id');
        $type = $request->input('type');
        $info = $withdrawService->getById($id);

        if ($type == 0) { //设置为已转账


            $info->state = 2;
            if ($info->save()) {

                //对应的链上转账记录更新为-1待处理
                $tradeService = resolve(TradeService::class);
                $tradeLog = $tradeService->one([
                    'no' => $info->id,
                    'action' => 'withdraw',
                    'module' => 'coin',
                ], [
                    'exception' => false
                ]);
                if ($tradeLog && $tradeLog->state != 2) {
                    $tradeLog->state = 2;
                    $tradeLog->save();
                }


                return ['message' => '设置成功'];
            } else {
                \Log::warning("设置后台提现信息ID" . $id . "状态为已转账成功,操作保存失败", $info->toArray());
                throw new AdminCommonException('设置失败');
            }
        } elseif ($type == 1) { //审核，弹出二次确认框。确认后将状态改为：-1

            if($info->state!=-2){
                throw new \Exception('该信息状态已改变，请刷新页面');
            }

            $state = $request->input('state','-2');
            return DB::transaction(function () use($info,$state){

                $info->state = -1;
                if ($info->save()) {

                    //对应的链上转账记录更新为-1待处理
                    $tradeService = resolve(TradeService::class);
                    $tradeLog = $tradeService->one([
                        'no' => $info->id,
                        'action' => 'withdraw',
                        'module' => 'coin',
                    ], [
                        'exception' => false
                    ]);
                    if ($tradeLog) {
                        if($tradeLog->state != -1){
                            $tradeLog->state = -2;
                            $tradeLog->save();
                        }
                    }else{


                        $tokenioVersion = CoinConfig::query()->where('symbol',$info->symbol)
                            ->where('chain',$info->chain)
                            ->where('withdraw_state',1)
                            ->value('tokenio_version') ?? 0;

                        if(empty($tokenioVersion)){
                            throw new \Exception('coin_config信息错误');
                        }

                        //根据不同的tokenio版本，找不同的系统钱包
                        $systemWalletService = resolve(SystemWalletService::class);
                        $systemWallet = $systemWalletService->getWithdrawWalletByChain($info->chain,$tokenioVersion);

                        //没有记录，添加一天链上转账记录
                        $trade = [
                            'user_id' => $info->user_id,
                            'from'    => $systemWallet->address,
                            'to'      => $info->to,
                            'symbol'  => $info->symbol,
                            'module'  => $options['module'] ?? 'coin',
                            'action'  => $options['action'] ?? CoinTrade::ACTION_WITHDRAW,
                            'weight'     => 100,
                            'no'      => $info->id,
                            'num'     => $info->num,
                            'state'   => $state,//CoinTrade::STATE_HUMAN_TRANSFER,
                            'chain'   => $info->chain,
                            'tokenio_version' => $tokenioVersion
                        ];

                        $tradeService->create($trade);
                    }

                    return ['message' => '设置成功'];
                } else {

                    throw new AdminCommonException('设置失败');
                }
            });



        } elseif ($type == 2) { //撤销，弹出二次确认框。确认后状态改为：-3。并将退回用户余额


            if($info->state!=-2){
                throw new \Exception('该信息状态已改变，请刷新页面');
            }

            DB::beginTransaction();
            try {

                $info->state = -3;
                if ($info->save()) {


                    //退回提现金额+手续费
                    $num = $info->num + $info->cost;
                    $balanceChangeService = resolve(BalanceChangeService::class);
                    $balanceChangeService
                        ->to($info->user_id)
                        ->withSymbol($info->symbol)
                        ->withNum($num)
                        ->withModule('coin.withdraw_return')
                        ->withInfo(new TranslateExpression('coin::message.后台撤销提现'))
                        ->change();

                    //对应的链上转账记录更新为-3被取消
                    $tradeService = resolve(TradeService::class);
                    $tradeLog = $tradeService->one([
                        'no' => $info->id,
                        'action' => 'withdraw',
                        'module' => 'coin',
                    ], [
                        'exception' => false
                    ]);
                    if ($tradeLog && $tradeLog->state != -3) {
                        $tradeLog->state = '-3';
                        $tradeLog->save();
                    }

                    DB::commit();


                    return ['message' => '设置成功'];
                } else {
                    Db::rollBack();
                    \Log::warning("设置后台提现信息ID" . $id . "状态为撤销状态，操作失败：", $info->toArray());
                    throw new AdminCommonException('设置失败');
                }
            } catch (\Exception $e) {

                Db::rollBack();
                \Log::warning("设置后台提现信息ID" . $id . "状态为撤销状态，程序异常：", [$e->getMessage()]);
                throw new AdminCommonException('设置失败：' . $e->getMessage());
            }

        } else {
            throw new AdminCommonException('状态类型错误');
        }
    }


    /**
     * 提现统计
     * 结果按币种分组，有几种币就产生多少条记录
     * @param Request $request
     * @param WithdrawService $withdrawService
     * @return mixed
     */
    public function total(Request $request, WithdrawService $withdrawService)
    {

        $param = $request->input('key');
        $where = [];
        if (!empty($param['times'])) {
            $time = explode('||', $param['times']);
            $time[0] = date('Y-m-d H:i:s', strtotime($time[0]));
            $time[1] = date('Y-m-d H:i:s', strtotime($time[1]) + 86400);
            $where[] = ['created_at', '>=', [$time[0]]];
            $where[] = ['created_at', '<=', [$time[1]]];
        }

        if (!empty($param['user_info'])) {

            $userService = resolve(UserService::class);
            $userInfo = $userService->one(['id' => $param['user_info']], [
                'exception' => false,
                'queryCallback' => function ($query) use ($param) {
                    $query->orWhere('username', $param['user_info'])->value('id');
                }
            ]);

            if (!$userInfo) {
                $userId = -1;
            } else {
                $userId = $userInfo->id;
            }
            $where[] = ['user_id', '=', $userId];
        }


        $list = $withdrawService->paginate($where, [
            'exception' => false,
            'queryCallback' => function ($query) {
                $query->select('symbol', 'state', \DB::raw('SUM(num) as total_num'))
                    ->groupBy('symbol', 'state');
            }
        ])->toArray();

        $data = $list['data'];
        $symbol = [];
        $total = [];
        if ($data) {

            //统计出每个币种已成功数量(1,2)和未成功数量(-1,0,-2,-4)
            foreach ($data as $item) {
                if (!in_array($item['symbol'], $symbol)) {
                    $symbol[$item['symbol']] = $item['symbol'];
                    $total[$item['symbol']] = [
                        'symbol' => $item['symbol'],
                        'fail_num' => 0,
                        'success_num' => 0
                    ];
                }

                //累积未成功
                if (in_array($item['state'], [-1, 0, -2,-3, -4])) {
                    $total[$item['symbol']]['fail_num'] += $item['total_num'];
                } else {
                    $total[$item['symbol']]['success_num'] += $item['total_num'];
                }
            }
        }

        $list['data'] = $total;

        return $list;
    }


}
