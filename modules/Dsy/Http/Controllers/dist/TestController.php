<?php


namespace Modules\Dsy\Http\Controllers\dist;


use App\Jobs\RecordJob;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Coin\Models\CoinTokenioNotice;
use Modules\Coin\Models\CoinTrade;
use Modules\Coin\Services\TokenioNoticeService;
use Modules\Coin\Services\TradeService;
use Modules\Dsy\Models\CoinAsset;
use Modules\Dsy\Models\Commodity;
use Modules\Dsy\Models\Message;
use Modules\Dsy\Models\News_user;
use Modules\Dsy\Models\Order;
use Modules\Dsy\Models\Record;
use Modules\Dsy\Models\Teams;
use Modules\Dsy\Models\Test;
use Modules\Dsy\Models\User;
use Modules\Dsy\Models\UserGrade;
use Modules\Dsy\Services\PublicService;

class TestController extends Controller
{





    //测试
    //立即支付
    public function payOrder(Request $request, PublicService $service)
    {
        $uid = $request->id;

        $params = $request->input();
        $params['id']=13;
        $num =$request->num ;


        $list = Commodity::query()->where('id', $params['id'])->first();


        $money = $list['money'] * $num;

        $save = $list['saves'] * $num;

        return \DB::transaction(function () use ($uid, $params, $list, $money, $num, $save) {


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
                'created_at' => date('Y-m-d H:i:s')
            ];

            $orderId=Order::query()->insertGetId($data);

            Commodity::subtract($params['id']);

            $job=[
                'chain'=>$list['chain'],
                'uid'=>$uid,
                'money'=>$money,
                'symbol'=>$list['symbol'],
                'order_id'=>$orderId
            ];

         //   RecordJob::dispatch($job)->onQueue('high');

            $teams = Teams::getTeams($list['chain']);

            $this->up($uid, $list['chain'], $teams,$money,$list['symbol'],$orderId);

            $grade = UserGrade::getGrade($uid, $list['chain']);

            $this->upDai($uid, $grade, $list['chain'], $teams);
        });
    }





    public function record($pgrade,$grade,$uid,$item,$money,$symbol,$chain,$teams,$pgrades,$orderId)
    {

        if ($grade>=2&&$grade<=6) {
            $list= [2=>$teams['a_shou'],3=>$teams['b_shou'],4=>$teams['c_shou'],5=>$teams['d_shou'],6=>$teams['e_shou']];
            $money = ($money * $list[$grade]);
            $data = [
                'user_id' => $item,
                'order_id'=>$orderId,
                'users' => $uid,
                'money' => $money,
                'symbol' => $symbol,
                'created_at' => date('Y-m-d H:i:s'),
                'type' => '销售提成',
                'chain' => $chain
            ];
            Record::query()->insert($data);
            $extracts = ['id' => $item, 'symbol' => $symbol, 'num' => $money, 'mid' => 758];
            extracts($extracts);
        }

        return['pgrade'=>$grade,'pgrades'=>$grade];
    }



    //新升级
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

    //分公司销售
    public function team($pgrade,$grade,$pid,$user_id,$num,$symbol,$chain,$teams,$pgrades,$orderId){
        $list= [0=>0,1=>0,2=>$teams['a_shou'],3=>$teams['b_shou'],4=>$teams['c_shou'],5=>$teams['d_shou'],6=>$teams['e_shou'],7=>$teams['f_shou']];
        $money=0;
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
        }

        if($pgrades>=$grade){
            $money=0;
        }

        if ($pgrades<$grade&&$pgrades>1){
            $money=$num*($list[$grade]-$list[$pgrades]);
        }

        if ($pgrade==6){
            $money = ($num * $list[7]);
        }

        if ($money>0){
            $data = [
                'user_id' => $user_id,
                'order_id'=>$orderId,
                'users' => $pid,
                'money' => $money,
                'symbol' => $symbol,
                'created_at' => date('Y-m-d H:i:s'),
                'type' => '销售提成',
                'chain' => $chain
            ];

            Record::query()->insert($data);

            $extracts = ['id' => $user_id, 'symbol' => $symbol, 'num' => $money, 'mid' => 758];
            extracts($extracts);
        }
        if ($grade>$pgrade&&$grade>$pgrades) $pgrades=$grade;
        if ($grade<=$pgrade&&$pgrade>$pgrades)  $pgrades=$pgrade;
        return['pgrade'=>$grade,'pgrades'=>$pgrades];
    }


    public function getGrade(Request $request){

        $uid=$request->id;
        $pid = pid($uid);
        foreach ($pid as $k=>$item) {
            $grade = UserGrade::getGrade($item, 'SWARM');
            echo $item.'-'.$grade."//";
        }
    }

    //代理升级
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


    //小区业绩
    public function plot($id,$chain)
    {
        return  Order::query()->where('user_id',$id)->where('chain',$chain)->sum('save');
    }

    //团队业绩
    public function getYj($id, $chain)
    {
        $id = User::query()->where('inviter_id', $id)->pluck('id');
        $num = Order::query()->whereIn('user_id', $id)->where('chain',$chain)->sum('save');
        foreach ($id as $datum) {
            $num = $num + $this->getYj($datum,$chain);
        }
        return $num;
    }


}
