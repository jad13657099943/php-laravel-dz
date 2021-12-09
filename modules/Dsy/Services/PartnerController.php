<?php


namespace Modules\Dsy\Services;


use Modules\Dsy\Models\Order;
use Modules\Dsy\Models\Record;
use Modules\Dsy\Models\Team;
use Modules\Dsy\Models\Teams;
use Modules\Dsy\Models\User;
use Modules\Dsy\Models\UserGrade;
use Modules\Dsy\Models\UserSy;

class PartnerController
{


    /**
     * 合伙人分红
     * @return mixed
     */
    public function index()
    {
        return \DB::transaction(function () {
            $time = date('Y-m-d');
            $end_time = date('Y-m-d', strtotime($time) + 86400);
            $where[] = ['created_at', '>=', $time];
            $where[] = ['created_at', '<', $end_time];

            $money = $this->getMoney($where);

            $sy = $this->getSy($where);

            $orderId=$this->getOrderId($where);

            $syOrderId=$this->getSyOrderId($where);

            $userId=$this->getUserId($where);

            $uidList = UserGrade::wherePluck([['grade', '>=', 5]], 'user_id');
            if (empty($uidList) || (empty($money) && empty($sy))) return true;

            $data = [];
            foreach ($uidList as $item) {
                $data[$item] = $this->getWeight($item);
            }

            $sum = array_sum($data);

            $list = Teams::getTeams('SWARM');
            $sum_money = $money * $list['g_shou'];
            $sum_sy = $sy * $list['g_fen'];

            $sy = [];
            foreach ($uidList as $item) {
                $balance = $sum_money * ($data[$item] / $sum);
                $this->setRecord($item, $balance, 'USDT', 'SWARM',$orderId,$data[$item] / $sum,$userId,$sum_money);

                $balances = $sum_sy * ($data[$item] / $sum);
                if ($balances > 0) {
                    $sy[] = [
                        'user_id' => $item,
                        'money' => $balances,
                        'symbol' => 'BZZ',
                        'chain' => 'SWARM',
                        'type' => '合伙人全球矿池分红',
                        'bl'=>$data[$item] / $sum,
                        'sum'=>$sum_sy,
                        'order_id'=>$syOrderId,
                        'created_at' =>$time
                    ];
                }
            }
            Team::query()->insert($sy);
        });
    }

    /**
     * 获取用户的权重
     * @param $uid
     * @return int
     */
    public function getWeight($uid)
    {
        $where[] = ['inviter_id', '=', $uid];
        $idList = User::wherePluck($where, 'id');
        $num = UserGrade::query()->whereIn('user_id', $idList)->where('grade', '>=', 5)->count();
        return $num + 1;
    }

    /**
     * 获取订单总金额
     * @param $where
     * @return int|mixed
     */
    public function getMoney($where)
    {
        return Order::whereSum($where, 'money');
    }

    /**
     * 获取订单编号
     * @param $where
     * @return string
     */
    public function getOrderId($where)
    {
         $id= Order::query()->where($where)->pluck('id')->toArray();
        return implode(',',$id);
    }

    /**
     * 收益订单编号
     * @param $where
     * @return string
     */
    public function getSyOrderId($where){
        $id= UserSy::query()->where($where)->pluck('order_id')->toArray();
        return implode(',',$id);
    }

    /**
     * 获取用户id
     * @param $where
     * @return string
     */
    public function getUserId($where){
        $id= Order::query()->where($where)->pluck('user_id')->toArray();
        return implode(',',$id);
    }

    /**
     * 获取挖矿收益
     * @param $where
     * @return int|mixed
     */
    public function getSy($where)
    {
        return UserSy::whereSum($where, 'money');
    }

    /**
     * 发放收益与记录
     * @param $uid
     * @param $money
     * @param $symbol
     * @param $chain
     * @param $orderId
     * @param $bl
     * @param $userId
     * @param $sum
     */
    public function setRecord($uid, $money, $symbol, $chain,$orderId,$bl,$userId,$sum)
    {
        if ($money <= 0) return;
        $money=round($money,6);
        $data = [
            'user_id' => $uid,
            'users'=>$userId,
            'order_id'=>$orderId,
            'money' => $money,
            'bl'=>$bl,
            'symbol' => $symbol,
            'created_at' => date('Y-m-d H:i:s'),
            'type' => '合伙人全球销售分红',
            'chain' => $chain,
            'sum'=>$sum
        ];

        Record::query()->insert($data);

        $extracts = ['id' => $uid, 'symbol' => $symbol, 'num' => $money, 'mid' => 758,'message'=>'合伙人全球销售分红'];
        extracts($extracts);
    }
}
