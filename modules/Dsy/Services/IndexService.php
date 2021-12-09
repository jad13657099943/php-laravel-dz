<?php


namespace Modules\Dsy\Services;


use Modules\Dsy\Models\CoinAsset;
use Modules\Dsy\Models\CoinLog;
use Modules\Dsy\Models\Commodity;
use Modules\Dsy\Models\Dsy\Chain;
use Modules\Dsy\Models\Dsy\Name;
use Modules\Dsy\Models\Dsy\Not;
use Modules\Dsy\Models\Dsy\Price;
use Modules\Dsy\Models\Order;
use Modules\Dsy\Models\Record;
use Modules\Dsy\Models\Team;
use Modules\Dsy\Models\Unite;
use Modules\Dsy\Models\UniteOrder;
use Modules\Dsy\Models\User;
use Modules\Dsy\Models\UserGrade;
use Modules\Dsy\Models\UserSy;

class IndexService
{

    /**
     * 栏目
     * @param int $limit
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function chainList($limit = 10)
    {
        return Chain::query()->paginate($limit);
    }

    /**
     * 联合挖矿
     * @param int $limit
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function uniteList($limit = 10)
    {

        return Unite::KPaginate($limit);
    }

    /**
     * 存储挖矿
     * @param $params
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function commodityList($params)
    {
        $where[] = ['chain', '=', $params['chain']];
        return Commodity::wherePaginate($where, $params['limit']);
    }

    /**
     * 我的订单
     * @param $params
     * @param $uid
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function orderList($params, $uid)
    {
        $where[] = ['user_id', '=', $uid];
        if (!empty($params['symbol'])) $where[] = ['to_symbol', '=', $params['symbol']];
        if ($params['type'] == 1) {
            $sql = Order::query()->where($where);
            if ($params['time'] == 2) $sql->orderBy('created_at', 'desc');
            if ($params['money'] == 2) $sql->orderBy('money', 'desc');
            $list = $sql->paginate($params['limit']);
        }
        if ($params['type'] == 2) {
            $sql = UniteOrder::query()->where($where);
            if ($params['time'] == 2) $sql->orderBy('created_at', 'desc');
            if ($params['money'] == 2) $sql->orderBy('money', 'desc');
            $list = $sql->paginate($params['limit']);
        }
        $data = $list->items();
        foreach ($data as $datum) {
            $datum->content = json_decode($datum->content, true);
            $datum->type = Order::$type[$datum->type];
        }
        return $list;
    }

    /**
     * 我的资产
     * @param $params
     * @param $uid
     * @return array
     */
    public function balanceList($params, $uid)
    {

        $symbol = $params['symbol'];
        $balance = CoinAsset::getBalance($uid, $symbol);
        $price = Price::getPrice($symbol);
        $estimate = $balance * $price;
        $expend = CoinLog::getBalance($uid, [['num', '<', 0]], $symbol);
        $income = CoinLog::getBalance($uid, [['num', '>', 0]], $symbol);
        $where[] = ['num', '>', 0];
        $where[] = ['action', '=', 'redeem'];
        $sy = CoinLog::getBalance($uid, $where, $symbol);
        $where2[] = ['num', '>', 0];
        $action = ['redeem', 'team', 'extract'];
        $zsy = CoinLog::getInBalance($uid, $where2, $symbol, $action);
        $start_time = date('Y-m-d');
        $end_time = date('Y-m-d', strtotime($start_time) + 86400);
        $where3[] = ['created_at', '>=', $start_time];
        $where3[] = ['created_at', '<', $end_time];
        $where3[] = ['num', '>', 0];
        $yesterday = CoinLog::getInBalance($uid, $where3, $symbol, $action);
        $not = Not::getNot($uid, $symbol);
        $data = [
            'balance' => $balance,
            'price' => $price,
            'estimate' => $estimate,
            'expend' => $expend,
            'income' => $income,
            'sy' => $sy,
            'zsy' => $zsy,
            'yesterday' => $yesterday,
            'not' => $not ?? 0
        ];

        return success($data);
    }

    /**
     * 我的收益
     * @param $params
     * @param $uid
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function syList($params, $uid)
    {

        $symbol = $params['symbol'];
        $type = $params['type'];
        $limit = $params['limit'];
        $where[] = ['user_id', '=', $uid];
        $where[] = ['symbol', '=', $symbol];
        if ($type == 1) {
            /*  $where[]=['state','=',1];
              return Total::getList($where, $limit);*/
            return UserSy::getList($where, $limit);
        }
        if ($type == 2) {
            return Record::getList($where, $limit);
        }
        if ($type == 3) {
            /*  $where[]=['state','=',2];
                return Total::getList($where, $limit);*/
            $list = Team::getList($where, $limit);
            /* foreach ($list->items() as $item){
                 $name=Name::getName();
                 if (in_array($item->type,Name::$type)){
                     $item->type=$name[$item->type].'奖励';
                 };
             }*/
            return $list;
        }
    }

    /**
     * 我的收入支出
     * @param $params
     * @param $uid
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function incomeExpensesList($params, $uid)
    {

        $symbol = $params['symbol'];
        $type = $params['type'];
        $limit = $params['limit'];
        $where[] = ['symbol', '=', $symbol];
        $where[] = ['user_id', '=', $uid];
        if ($type == 1) {
            $where[] = ['num', '>', 0];
            $sql = CoinLog::query()->where($where);
            if ($params['time'] == 2) $sql->orderBy('created_at', 'desc');
            if ($params['money'] == 2) $sql->orderBy('money', 'desc');
            $list = $sql->paginate($limit);

        }
        if ($type == 2) {
            $where[] = ['num', '<', 0];
            $sql = CoinLog::query()->where($where);
            if ($params['time'] == 2) $sql->orderBy('created_at', 'desc');
            if ($params['money'] == 2) $sql->orderBy('money', 'desc');
            $list = $sql->paginate($limit);

        }
        $data = $list->items();
        foreach ($data as $datum) {
            $info = json_decode($datum->info, true);
            if ($datum->infos = trans($info['key']) == '内部转账扣除' || $datum->infos = trans($info['key']) == '内部转账增加') {
                if ($datum['num'] > 0) {
                    $to = $datum->user_id;
                    $from = $info['params']['from'];
                } else {
                    $to = $info['params']['to'];
                    $from = $datum->user_id;
                }
                $datum->infos = trans($info['key']) . ',转出用户uid:' . $from . ',转入用户uid:' . $to . ',转出币种:' . $info['params']['symbol'];
            } else {
                $datum->infos = trans($info['key']);
            }

        }
        return $list;
    }

    /**
     * 我的信息
     * @param $uid
     * @return array
     */
    public function userMessage($uid)
    {

        $symbol = 'FIL';
        $fil = CoinAsset::getBalance($uid, $symbol);
        $usdt = CoinAsset::getBalance($uid, 'USDT');
        $where2[] = ['num', '>', 0];
        $action = ['redeem', 'team', 'extract'];
        $zsy = CoinLog::getInBalance($uid, $where2, $symbol, $action);
        $zhi = Order::getFil($uid);
        $price = Price::getPrice($symbol);
        $zhe = $usdt + $fil * $price;
        $data = [
            'fil' => $fil,
            'usdt' => $usdt,
            'zsy' => $zsy,
            'zhi' => $zhi,
            'zhe' => $zhe
        ];
        return success($data);
    }


    /**
     * 我的等级
     * @param $uid
     * @return \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public function userGrade($uid)
    {
        $list = UserGrade::query()->where('user_id', $uid)->get();
        $name = Name::getName();
        foreach ($list as $item) {
            $item->grade = $name[Name::$name[$item->grade]];
        }
        return $list;
    }

    /**
     * 邀请信息
     * @param $uid
     * @return array
     */
    public function invite($uid)
    {

        $start_time = date('Y-m-d');
        $end_time = date('Y-m-d', strtotime($start_time) + 86400);
        $where[1] = ['inviter_id', '=', $uid];
        $where[2] = ['created_at', '>=', $start_time];
        $where[3] = ['created_at', '<', $end_time];
        $lei = User::whereCount([$where[1]]);
        $today = User::whereCount($where);
        $data = [
            'lei' => $lei,
            'today' => $today
        ];
        return success($data);
    }

    /**
     * 币种列表
     * @param $uid
     * @return \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public function symbolList($uid)
    {

        $list = Price::query()->get();
        foreach ($list as $item) {
            $balance = CoinAsset::getBalance($uid, $item->symbol);
            $item->balance = $balance;
            $item->zhe = $balance * $item->price;
        }
        return $list;
    }

    /**
     * 手续费
     * @param $params
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|object|null
     */
    public function freeList($params)
    {

        $symbol = $params['symbol'];
        $free = Price::query()->where('symbol', $symbol)->first();
        $free['data'] = $free['free'];
        return $free;
    }
}
