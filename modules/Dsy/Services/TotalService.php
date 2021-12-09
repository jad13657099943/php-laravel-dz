<?php


namespace Modules\Dsy\Services;


use Modules\Coin\Models\CoinAsset;
use Modules\Dsy\Models\CoinLog;
use Modules\Dsy\Models\Dsy\Price;
use Modules\Dsy\Models\Dsy\Total;
use Modules\Dsy\Models\Order;
use Modules\Dsy\Models\Record;
use Modules\Dsy\Models\Team;
use Modules\Dsy\Models\UserSy;

class TotalService
{

    /**
     * 统计基础收益
     */
    public function total()
    {
        $time = date('Y-m-d');
        $end_time = date('Y-m-d', strtotime($time) + 86400);
        $where[] = ['created_at', '>=', $time];
        $where[] = ['created_at', '<', $end_time];
        $symbol = UserSy::query()->where($where)->distinct()->pluck('symbol');
        $uid = UserSy::query()->where($where)->distinct()->pluck('user_id');
        \DB::transaction(function () use ($where, $symbol, $uid, $time) {
            $data = [];
            foreach ($uid as $item) {
                foreach ($symbol as $value) {
                    $money = UserSy::query()->where($where)->where('user_id', $item)->where('symbol', $value)->sum('money');
                    if ($money <= 0) continue;
                    $data[] = [
                        'user_id' => $item,
                        'money' => $money,
                        'symbol' => $value,
                        'type' => '每日挖矿',
                        'created_at' => $time
                    ];
                    $redeem = ['id' => $item, 'symbol' => $value, 'num' => $money, 'mid' => 0, 'message' => '每日挖矿'];
                    redeem($redeem);
                }
            }
            Total::query()->insert($data);
        });
    }


    /**
     * 统计团队收益
     */
    public function totals()
    {
        $time = date('Y-m-d');
        $end_time = date('Y-m-d', strtotime($time) + 86400);
        $where[] = ['created_at', '>=', $time];
        $where[] = ['created_at', '<', $end_time];
        $symbol = Team::query()->where($where)->distinct()->pluck('symbol');
        $uid = Team::query()->where($where)->distinct()->pluck('user_id');
        \DB::transaction(function () use ($where, $symbol, $uid, $time) {
            $data = [];
            foreach ($uid as $item) {
                foreach ($symbol as $value) {
                    $money = Team::query()->where($where)->where('user_id', $item)->where('symbol', $value)->sum('money');
                    if ($money <= 0) continue;
                    $data[] = [
                        'user_id' => $item,
                        'money' => $money,
                        'symbol' => $value,
                        'state' => 2,
                        'type' => '矿池分红',
                        'created_at' => $time
                    ];
                    $team = ['id' => $item, 'symbol' => $value, 'num' => $money, 'mid' => 0, 'message' => '矿池分红'];
                    redeem($team);
                }
            }
            Total::query()->insert($data);
        });
    }

    /**
     * 矫正余额
     */
    public function del()
    {
        $list = CoinAsset::query()->distinct()->pluck('user_id');
        \DB::transaction(function () use ($list) {
            $symbol = Price::query()->distinct()->pluck('symbol');
            foreach ($list as $item) {
                foreach ($symbol as $value) {
                    $num = CoinLog::query()->where('user_id', $item)->where('symbol', $value)->sum('num');
                    if ($num <= 0) $num = 0;
                    CoinAsset::query()->where('user_id', $item)->where('symbol', $value)->update(['balance' => $num]);
                }
            }
        });
    }

    /**
     * 计算比例
     */
    public function bl()
    {
        $list = Record::query()->where('type', '!=', '合伙人全球销售分红')->get();
        $data = Order::query()->select('id', 'money')->get();
        $order = [];
        foreach ($data as $datum) {
            $order[$datum->id] = $datum->money;
        }
        foreach ($list as $item) {
            Record::query()->where('id', $item->id)->update(['bl' => $item->money / $order[$item->order_id]]);
        }
    }

    /**
     * 计算订单金额
     */
    public function sum()
    {
        $list = Record::query()->where('type', '!=', '合伙人全球销售分红')->get();
        $data = Order::query()->select('id', 'money')->get();
        $order = [];
        foreach ($data as $datum) {
            $order[$datum->id] = $datum->money;
        }
        foreach ($list as $item) {
            Record::query()->where('id', $item->id)->update(['sum' => $order[$item->order_id]]);
        }
    }

    /**
     * 计算全球订单金额
     */
    public function global()
    {
        $list = Record::query()->where('type', '=', '合伙人全球销售分红')->get();
        foreach ($list as $item) {
            $sum = Order::query()->whereIn('id', explode(',', $item->order_id))->sum('money');
            Record::query()->where('id', $item->id)->update(['sum' => $sum * 0.01]);
        }
    }

    /**
     * 计算全球比例
     */
    public function globalBl()
    {
        $list = Record::query()->where('type', '=', '合伙人全球销售分红')->get();
        foreach ($list as $item) {
            Record::query()->where('id', $item->id)->update(['bl' => $item->money / $item->sum]);
        }
    }

    /**
     * 矫正来源
     */
    public function users()
    {
        $list = Record::query()->where('type', '=', '合伙人全球销售分红')->get();
        foreach ($list as $item) {
            $user = Order::query()->whereIn('id', explode(',', $item->order_id))->pluck('user_id')->toArray();
            Record::query()->where('id', $item->id)->update(['users' => implode(',', $user)]);
        }
    }
}
