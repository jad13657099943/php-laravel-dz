<?php


namespace Modules\Coin\Http\Controllers\Admin\Api;


use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Coin\Services\CoinService;
use Modules\Coin\Services\RechargeService;
use Modules\Core\Services\Frontend\UserService;
use Modules\Dsy\Models\User;

class RechargeApiController extends Controller
{

    public function index(Request $request, RechargeService $rechargeService)
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

        if (!empty($param['account'])){
            if (!filter_var($param['account'], FILTER_VALIDATE_EMAIL)) {
                $id=User::query()->where('mobile',$param['account'])->value('id');
                $where[] = ['user_id', '=', $id];
            }else{
                $id=User::query()->where('email',$param['account'])->value('id');
                $where[] = ['user_id', '=', $id];
            }
        }

        $list = $rechargeService->paginate($where, [
            'with' => ['user', 'coin'],
            'orderBy' => ['id', 'desc'],
        ]);

        $coinService = resolve(CoinService::class);
        foreach ($list as $item) {
            $item->mobile=$item->user['mobile'];
            $item->email=$item->user['email'];
            $item->username = $item->user->username;
            $item->value_text = $item->value . $item->symbol;
            $item->from_text = substr($item->from, 0, 4) . '****' . substr($item->from, -4, 4);
            $item->to_text = substr($item->to, 0, 4) . '****' . substr($item->to, -4, 4);
            $item->hash_text = empty($item->hash) ? '' : substr($item->hash, 0, 4) . '****' . substr($item->hash, -4, 4);
            $item->state_text = $item->state_name;

            //返回钱包地址跳转到区块链浏览器查询网址
            $item->from_link = $coinService->queryChainLink($item->chain, $item->from);
            $item->to_link = $coinService->queryChainLink($item->chain, $item->to);
            $item->hash_link = $coinService->queryChainLink($item->chain, null, $item->hash);

        }

        return $list;
    }


    /**
     * 获取跳转外链链接
     * @param Request $request
     * @return mixed
     */
    public function link(Request $request)
    {


        $id = $request->input('id');
        $rechargeService = resolve(RechargeService::class);
        $info = $rechargeService->getById($id, [
            'with' => 'coin'
        ]);

        $coinService = resolve(CoinService::class);
        if (!empty($info['hash'])) {
            $res = $coinService->queryChainLink($info->coin->chain, '', $info->hash);
        } else {
            $res = $coinService->queryChainLink($info->coin->chain, $info->from, '');
        }

        return ['url' => $res];
    }


    /**
     * 充值统计
     * 结果按币种分组，有几种币就产生多少条记录
     * @param Request $request
     * @param RechargeService $rechargeService
     * @return
     */
    public function total(Request $request, RechargeService $rechargeService)
    {

        $where[] = ['state', '=', 2];
        $param = $request->input('key');
        if (!empty($param['times'])) {
            $time = explode('||', $param['times']);
            $time[0] = date('Y-m-d H:i:s', strtotime($time[0]));
            $time[1] = date('Y-m-d H:i:s', strtotime($time[1]) + 86400);
            if ($param['time_type'] == 0) {
                $where[] = ['created_at', '>=', [$time[0]]];
                $where[] = ['created_at', '<=', [$time[1]]];
            } else {
                $where[] = ['packaged_at', '>=', [$time[0]]];
                $where[] = ['packaged_at', '<=', [$time[1]]];
            }
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


        $data = $rechargeService->paginate($where, [
            'exception' => false,
            'queryCallback' => function ($query) {
                $query->select('symbol', \DB::raw('SUM(value) as total_value'))
                    ->groupBy('symbol');
            }
        ])->toArray();

        return $data;
    }

}
