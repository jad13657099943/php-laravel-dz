<?php


namespace Modules\Dsy\Services;


use Modules\Core\Services\Traits\HasQuery;
use Modules\Dsy\Models\Dsy\Chain;
use Modules\Dsy\Models\Order;
use Modules\Dsy\Models\Release;
use Modules\Dsy\Models\Team;
use Modules\Dsy\Models\Teams;
use Modules\Dsy\Models\UserGrade;
use Modules\Dsy\Models\UserSy;
use Modules\Dsy\Models\UserSys;


class UserSyService
{
    use HasQuery;

    public $model;

    public function __construct(UserSy $model)
    {
        $this->model = $model;
    }

    /**
     * 基础释放
     * @return mixed
     */
    public function Release()
    {
        // $award = Release::getRelease();
        $time = date('Y-m-d');
        $where[] = ['start_time', '<=', $time];
        $where[] = ['start_time', '>', 0];
        $where[] = ['end_time', '>', 0];
        $where[] = ['end_time', '>', $time];
        $list = Order::getOrder($where);
        return \DB::transaction(function () use ($where, $list) {
            $time = date('Y-m-d H:i:s');
            $sy = [];
            $sys = [];
            $T = $this->getNum('SWARM');
            if ($T <= 0) return;
            foreach ($list as $item) {
                $jc = 0;
                $xx = 0;
                if ($item->to_type == 1) {
                    if ($item->fil_state < 1) {
                        $jc = $item->save * $T * $item->bl * 0.25;
                        $xx = $item->save * $T * $item->bl * 0.75;
                    }
                    if ($item->fil_state > 0) {
                        if ($item->need_fil > 0) {
                            $jc = $item->save * $T * $item->bl * 0.25 * ($item->to_fil / $item->need_fil);
                            $xx = $item->save * $T * $item->bl * 0.75 * ($item->to_fil / $item->need_fil);
                        } else {
                            $jc = $item->save * $T * $item->bl * 0.25;
                            $xx = $item->save * $T * $item->bl * 0.75;
                        }

                    }
                    if ($jc > 0) {
                        $sy[] = ['user_id' => $item->user_id,
                            'order_id' => $item->id,
                            'money' => $jc,
                            'symbol' => $item->to_symbol,
                            'type' => '每日挖矿(基础)',
                            'created_at' => $time,
                            'chain' => $item->chain
                        ];
                    }
                    if ($xx > 0) {
                        $sys[] = ['user_id' => $item->user_id,
                            'order_id' => $item->id,
                            'money' => $xx,
                            'symbol' => $item->to_symbol,
                            'type' => '每日挖矿(线性冻结)',
                            'end_time' => date('Y-m-d H:i:s', strtotime('+180 day', time())),
                            'created_at' => $time,
                            'chain' => $item->chain
                        ];
                    }

                }
                if ($item->to_type == 2) {
                    if ($item->fil_state < 1) {
                        $xx = $item->save * $T * $item->bl;
                    }
                    if ($item->fil_state > 0) {
                        $xx = $item->save * $T * $item->bl * ($item->to_fil / $item->need_fil);
                    }
                    if ($xx > 0) {
                        $sys[] = ['user_id' => $item->user_id,
                            'order_id' => $item->id,
                            'money' => $xx,
                            'symbol' => $item->to_symbol,
                            'type' => '每日挖矿(线性冻结)',
                            'end_time' => date('Y-m-d H:i:s', strtotime('+180 day', time())),
                            'created_at' => $time,
                            'chain' => $item->chain
                        ];
                    }

                }
                if ($item->to_type == 3) {
                    if ($item->fil_state < 1) {
                        $jc = $item->save * $T * $item->bl;
                    }
                    if ($item->fil_state > 0) {
                        $jc = $item->save * $T * $item->bl * ($item->to_fil / $item->need_fil);
                    }
                    if ($jc > 0) {
                        $sy[] = ['user_id' => $item->user_id,
                            'order_id' => $item->id,
                            'money' => $jc,
                            'symbol' => $item->to_symbol,
                            'type' => '每日挖矿',
                            'created_at' => $time,
                            'chain' => $item->chain
                        ];
                    }

                }

                /* if ($jc>0){
                     $redeem = ['id' => $item->user_id, 'symbol' => $item->to_symbol, 'num' => $jc, 'mid' => 0, 'message' => '收益'];
                     redeem($redeem);
                 }*/
            }
            UserSy::query()->insert($sy);
            UserSys::query()->insert($sys);

        });
    }

    /**
     * 每节点数产出
     * @param $chain
     * @return false|float
     */
    public function getNum($chain)
    {
        $day = 0;
        $sum = 0;
        $list = Release::query()->where('chain', $chain)->first();
        $time = date('Y-m-d H:i:s');
        if ($time >= $list['start_at'] && $time < $list['end_at']) {
            if (!empty($list['end_at']) && !empty($list['start_at'])) {
                $day = (strtotime($list['end_at']) - strtotime($list['start_at'])) / 86400;
            }
            $day_num = $list['value'] / $day;
            $save = Order::query()->where('chain', $chain)->sum('save');
            if ($save > 0 && $day_num > 0) {
                $sum = $day_num / $save;
            }
        }
        return round($sum, 6);
    }

    /**
     * 线性释放模板
     */
    public function xianRelease()
    {
        $time = date('Y-m-d');
        $where[] = ['end_time', '>=', $time];
        $where[] = ['money', '>', 0];
        \DB::transaction(function () use ($where) {
            $time = date('Y-m-d H:i:s');
            // $list=UserSys::getUserSys($where);
            $list = UserSys::getUid($where);
            $chain = Chain::getChain();
            if (!empty($list)) {
                $data = [];
                foreach ($list as $item) {
                    foreach ($chain as $value) {
                        $money = UserSys::getUserMoney($item, $value) / 180;
                        if ($money <= 0) continue;
                        $data[] = [
                            'user_id' => $item,
                            'order_id' => 9999,
                            'money' => $money,
                            'symbol' => Chain::$state[$value],
                            'type' => '每日挖矿(线性)',
                            'created_at' => $time,
                            'chain' => $value
                        ];
                        /*  $redeem = ['id' => $item, 'symbol' => Chain::$state[$value], 'num' => $money, 'mid' => 0, 'message' => '收益'];
                          redeem($redeem);*/
                    }
                }
                UserSy::query()->insert($data);
            }
        });
    }

    /**
     * 每日释放团队
     * @return mixed
     */
    public function teamRelease()
    {
        $time = date('Y-m-d');
        $end_time = date('Y-m-d', strtotime($time) + 86400);

        $where[1] = ['created_at', '>=', $time];
        $where[2] = ['created_at', '<', $end_time];
        $where[3] = ['money', '>', 0];
        $where[4] = ['chain', '!=', 'UNITE'];
        return \DB::transaction(function () use ($where) {
            $userId = UserSy::getUser($where);
            // $userId = UserSy::getUserSy($where);
            $teams = Teams::getTeams('SWARM');
            foreach ($userId as $item) {
                $pid = pid($item->user_id);
                $Pgrade = 0;
                $pgrades = 0;
                $where[5] = ['user_id', '=', $item->user_id];
                $orderId = UserSy::getOrderId($where);
                foreach ($pid as $k => $value) {
                    $grade = UserGrade::getGrade($value, $item->chain);
                    if ($k < 1 && $grade >= 2 && $grade <= 6) {
                        $data = $this->dai($value, $item->user_id, $Pgrade, $item->money, $item->chain, $item->symbol, $grade, $teams, $pgrades, $orderId);
                        $Pgrade = $data['Pgrade'];
                        $pgrades = $data['pgrades'];
                    }
                    if ($k > 0) {
                        $data = $this->team($value, $item->user_id, $Pgrade, $item->money, $item->chain, $item->symbol, $grade, $teams, $pgrades, $orderId);
                        $Pgrade = $data['Pgrade'];
                        $pgrades = $data['pgrades'];
                    }
                }
            }
        });
    }

    /**
     * 代理每日释放
     * @param $value
     * @param $users
     * @param $Pgrade
     * @param $money
     * @param $chain
     * @param $symbol
     * @param $grade
     * @param $teams
     * @param $pgrades
     * @param $orderId
     * @return array
     */
    public function dai($value, $users, $Pgrade, $money, $chain, $symbol, $grade, $teams, $pgrades, $orderId)
    {
        $list = [2 => $teams['a_fen'], 3 => $teams['b_fen'], 4 => $teams['c_fen'], 5 => $teams['d_fen'], 6 => $teams['e_fen'], 7 => $teams['f_fen']];
        $num = $money * $list[$grade];
        $type = '直推矿池分红';
        $bl = $list[$grade];
        $usersGrade = UserGrade::getGrade($users, $chain);
        if ($usersGrade == 6) {
            $num = $money * ($list[$grade] + $list[7]);
            $type = '直推分公司矿池分红';
            $bl = $list[$grade] + $list[7];
        }
        $this->setTeam($value, $users, $num, $type, $chain, $symbol, $orderId, $bl, $money);
        return ['Pgrade' => $grade, 'pgrades' => $grade];
    }

    /**
     * 团队每日释放
     * @param $value
     * @param $users
     * @param $Pgrade
     * @param $money
     * @param $chain
     * @param $symbol
     * @param $grade
     * @param $teams
     * @param $pgrades
     * @param $orderId
     * @return array
     */
    public function team($value, $users, $Pgrade, $money, $chain, $symbol, $grade, $teams, $pgrades, $orderId)
    {
        $list = [0 => 0, 1 => 0, 2 => $teams['a_fen'], 3 => $teams['b_fen'], 4 => $teams['c_fen'], 5 => $teams['d_fen'], 6 => $teams['e_fen'], 7 => $teams['f_fen']];
        $num = 0;
        $type = '级差矿池分红';
        $bl = 0;
        if ($grade > $Pgrade) {
            $num = $money * ($list[$grade] - $list[$Pgrade]);
            $bl = $list[$grade] - $list[$Pgrade];
        }

        if ($grade <= $Pgrade) {
            $num = 0;
        }

        if ($pgrades >= $grade) {
            $num = 0;
        }

        if ($pgrades < $grade && $pgrades > 1) {
            $num = $money * ($list[$grade] - $list[$pgrades]);
            $bl = $list[$grade] - $list[$pgrades];
        }

        /*   if ($Pgrade==6){
               $num = ($money * $list[7]);
               $type='直推分公司矿池分红';
               $bl=$list[7];
           }*/

        if ($grade > $Pgrade && $grade > $pgrades) $pgrades = $grade;
        if ($grade <= $Pgrade && $Pgrade > $pgrades) $pgrades = $Pgrade;
        $this->setTeam($value, $users, $num, $type, $chain, $symbol, $orderId, $bl, $money);
        return ['Pgrade' => $grade, 'pgrades' => $pgrades];
    }

    /**
     * 奖励记录
     * @param $value
     * @param $users
     * @param $money
     * @param $type
     * @param $chain
     * @param $symbol
     * @param $orderId
     * @param $bl
     * @param $sum
     */
    public function setTeam($value, $users, $money, $type, $chain, $symbol, $orderId, $bl, $sum)
    {


        if ($money > 0) {

            $data = [
                'user_id' => $value,
                'users' => $users,
                'money' => $money,
                'symbol' => $symbol,
                'chain' => $chain,
                'type' => $type,
                'order_id' => implode(',', $orderId),
                'bl' => $bl,
                'sum' => $sum,
                'created_at' => date('Y-m-d H:i:s')
            ];
            Team::query()->insert($data);
            /*$team = ['id' => $value, 'symbol' => $symbol, 'num' => $money, 'mid' => 0, 'message' => '收益'];
            team($team);*/
        }
    }
}
