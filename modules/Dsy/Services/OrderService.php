<?php


namespace Modules\Dsy\Services;


use Modules\Core\Services\Traits\HasQuery;
use Modules\Dsy\Models\CoinAsset;
use Modules\Dsy\Models\Commodity;
use Modules\Dsy\Models\Message;
use Modules\Dsy\Models\Order;
use Modules\Dsy\Models\Record;
use Modules\Dsy\Models\User;
use Modules\Dsy\Models\UserGrade;


class OrderService
{

    /**
     * 下单条件
     * @param $cid
     * @param $uid
     * @param $num
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|object|null
     * @throws \Exception
     */
    public function verification($cid, $uid, $num)
    {

        $list = Commodity::query()->where('id', $cid)->first();

        if ($list['residual_fraction']<1) throw new \Exception(trans("dsy::message.库存不足"));

        if ($list['state'] != 1) throw new \Exception(trans("dsy::message.已下架"));

        $usdt = CoinAsset::getUsdt($uid);

        if ($usdt < $list['money'] * $num) throw new \Exception(trans("dsy::message.余额不足"));

        return $list;
    }

    /**
     * 生成订单
     * @param $list
     * @param $save
     * @param $uid
     * @param $num
     * @param $money
     * @param $date
     * @param int $type
     * @return int
     */
    public function createOrder($list,$save,$uid,$num,$money,$date,$type=1){
        $need_fil = 0;
        $fil_state = 0;

        $start_time = date('Y-m-d H:i:s', time() + 86400 * $list['start_day']);
        $end_time = date('Y-m-d H:i:s', time() + 86400 * $list['period']);

        if ($list['pledge'] > 0) {
            $zhi = Message::getMessage('zhi');
            $need_fil = $zhi * $save;
            $fil_state = 1;
            $start_time = 0;
            $end_time = 0;
        }

        $data = [
            'user_id' => $uid,
            'content' => json_encode($list),
            'num' => $num,
            'save' => $save,
            'money' => $money,
            'need_fil' => $need_fil,
            'fil_state' => $fil_state,
            'chain' => $list['chain'],
            'bl' => $list['bl'],
            'to_type'=>$list['to_type'],
            'to_symbol'=>$list['to_symbol'],
            'start_time' => $start_time,
            'end_time' => $end_time,
            'created_at' => $date,
            'audit_at'=>date('Y-m-d H:i:s'),
            'type'=>$type
        ];

      return  $orderId=Order::query()->insertGetId($data);
    }

    /**
     * 直推
     * @param $pgrade
     * @param $grade
     * @param $uid
     * @param $item
     * @param $money
     * @param $symbol
     * @param $chain
     * @param $teams
     * @param $pgrades
     * @param $orderId
     * @return array
     */
    public function record($pgrade,$grade,$uid,$item,$money,$symbol,$chain,$teams,$pgrades,$orderId)
    {

        if ($grade>=2&&$grade<=6) {
            $sum=$money;
            $list= [2=>$teams['a_shou'],3=>$teams['b_shou'],4=>$teams['c_shou'],5=>$teams['d_shou'],6=>$teams['e_shou'],7=>$teams['f_shou']];
            $usersGrade = UserGrade::getGrade($uid, $chain);
            $money = ($money * $list[$grade]);
            $bl=$list[$grade];
            $type='直推奖励';
            if ($usersGrade==6){
                $money = $money * ($list[$grade]+$list[7]);
                $bl=$list[$grade]+$list[7];
                $type='直推分公司奖励';
            }
            $data = [
                'user_id' => $item,
                'order_id'=>$orderId,
                'bl'=>$bl,
                'users' => $uid,
                'money' => $money,
                'symbol' => $symbol,
                'created_at' => date('Y-m-d H:i:s'),
                'type' =>$type,
                'chain' => $chain,
                'sum'=>$sum
            ];
            $mid= Record::query()->insertGetId($data);
            $extracts = ['id' => $item, 'symbol' => $symbol, 'num' => $money, 'mid' => $mid,'message'=>$type];
            extracts($extracts);
        }

        return['pgrade'=>$grade,'pgrades'=>$grade];
    }

    /**
     * 新升级
     * @param $uid
     * @param $chain
     * @param $teams
     * @param $money
     * @param $symbol
     * @param $orderId
     */
    public function up($uid, $chain, $teams,$money,$symbol,$orderId)
    {
        $pid = pid($uid);
        $pgrade=0;
        $pgrades=0;
        foreach ($pid as $k=>$item) {
            $grade = UserGrade::getGrade($item, $chain);
            if($k<1){
                $data=$this->record($pgrade,$grade,$uid,$item,$money,$symbol,$chain,$teams,$pgrades,$orderId);
                $pgrade=$data['pgrade'];
                $pgrades=$data['pgrades'];
            }
            if ($k>0){
                $data=  $this->team($pgrade,$grade,$uid,$item,$money,$symbol,$chain,$teams,$pgrades,$orderId);
                $pgrade=$data['pgrade'];
                $pgrades=$data['pgrades'];
            }
            $this->upDai($item, $grade, $chain, $teams);
        }
    }

    /**
     * 分公司销售
     * @param $pgrade
     * @param $grade
     * @param $pid
     * @param $user_id
     * @param $num
     * @param $symbol
     * @param $chain
     * @param $teams
     * @param $pgrades
     * @param $orderId
     * @return array
     */
    public function team($pgrade,$grade,$pid,$user_id,$num,$symbol,$chain,$teams,$pgrades,$orderId){
        $list= [0=>0,1=>0,2=>$teams['a_shou'],3=>$teams['b_shou'],4=>$teams['c_shou'],5=>$teams['d_shou'],6=>$teams['e_shou'],7=>$teams['f_shou']];
        $type='级差奖励';
        $money=0;
        $bl=0;
        /* if ($grade==6){
             $money = ($num * $list[$grade]);
         }
         if ($pgrade==6){
             $money = ($num * $list[7]);
         }
         if ($pgrades==6&&$grade==6&&$pgrade!=6){
             $money=0;
         }*/
        if ($grade>$pgrade){
            $money=$num*($list[$grade]-$list[$pgrade]);
            $bl=($list[$grade]-$list[$pgrade]);
        }

        if($pgrades>=$grade){
            $money=0;
        }

        if ($pgrades<$grade&&$pgrades>1){
            $money=$num*($list[$grade]-$list[$pgrades]);
            $bl=($list[$grade]-$list[$pgrades]);
        }

       /* if ($pgrade==6){
            $money = ($num * $list[7]);
            $type='直推分公司奖励';
            $bl=$list[7];
        }*/

        if ($money>0){
            $data = [
                'user_id' => $user_id,
                'order_id'=>$orderId,
                'bl'=>$bl,
                'users' => $pid,
                'money' => $money,
                'symbol' => $symbol,
                'created_at' => date('Y-m-d H:i:s'),
                'type' => $type,
                'chain' => $chain,
                'sum'=>$num
            ];

            $mid= Record::query()->insertGetId($data);
            $extracts = ['id' => $user_id, 'symbol' => $symbol, 'num' => $money, 'mid' => $mid,'message'=>$type];
            extracts($extracts);
        }
        if ($grade>$pgrade&&$grade>$pgrades) $pgrades=$grade;
        if ($grade<=$pgrade&&$pgrade>$pgrades)  $pgrades=$pgrade;
        return['pgrade'=>$grade,'pgrades'=>$pgrades];
    }



    /**
     * 代理升级
     * @param $id
     * @param $grade
     * @param $chain
     * @param $teams
     */
    public function upDai($id, $grade, $chain, $teams)
    {
        $xiao = [2 => $teams['a_xiao'], 3 => $teams['b_xiao'], 4 => $teams['c_xiao'], 5 => $teams['d_xiao']];
        $da = [2 => $teams['a_da'], 3 => $teams['b_da'], 4 => $teams['c_da'], 5 => $teams['d_da']];
        if ($grade >= 1 && $grade < 5) {
            $plot = $this->plot($id,$chain);
            $team = $this->getYj($id,$chain);
            $level = 0;
            for ($i = 2; $i <= 5; $i++) {
                if ($plot >= $xiao[$i] && $team >= $da[$i]) {
                    $level = $i;
                }
            }
            if ($level > $grade) {
                UserGrade::setGrade($id, $chain, $level);
            }
        }
    }


    /**
     * 小区业绩
     * @param $id
     * @param $chain
     * @return int|mixed
     */
    public function plot($id,$chain)
    {
        return  Order::query()->where('user_id',$id)->where('chain',$chain)->sum('save');
    }

    /**
     * 团队业绩
     * @param $id
     * @param $chain
     * @return int|mixed
     */
    public function getYj($id,$chain)
    {
        $id = User::query()->where('inviter_id', $id)->pluck('id');
        $num = Order::query()->whereIn('user_id', $id)->where('chain',$chain)->sum('save');
        foreach ($id as $datum) {
            $num = $num + $this->getYj($datum,$chain);
        }
        return $num;
    }
}
