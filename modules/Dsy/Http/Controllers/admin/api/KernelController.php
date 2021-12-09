<?php


namespace Modules\Dsy\Http\Controllers\admin\api;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Coin\Models\Coin;
use Modules\Coin\Models\CoinConfig;
use Modules\Coin\Services\BalanceChangeService;
use Modules\Core\Translate\TranslateExpression;
use Modules\Dsy\Models\Commodity;
use Modules\Dsy\Models\Dsy\Chain;
use Modules\Dsy\Models\Dsy\Name;
use Modules\Dsy\Models\Dsy\Price;
use Modules\Dsy\Models\Dsy\Save;
use Modules\Dsy\Models\Message;
use Modules\Dsy\Models\Order;
use Modules\Dsy\Models\Release;
use Modules\Dsy\Models\Teams;
use Modules\Dsy\Models\User;
use Modules\Dsy\Models\UserGrade;


class KernelController extends Controller
{
    public function index()
    {
        return Price::query()->paginate(10);
    }

    public function update(Request $request)
    {
        $params = $request->input();
        $id = $params['id'];
        unset($params['id']);
        if (isset($params['symbol'])) unset($params['symbol']);
        $state = Price::query()->where('id', $id)->update($params);
        return ReturnCode($state);
    }

    public function index2()
    {

        $list=Release::query()->paginate(10);
        $day=0;
        $sum=0;
        foreach ($list->items() as $item){
            if (!empty($item->end_at)&&!empty($item->start_at)){
               $day=(strtotime($item->end_at)-strtotime($item->start_at))/86400;
            }
            $item->day=$day;
            $day_num=$item->value/$day;
            $item->day_num=$day_num;
            $save=Order::query()->where('chain',$item->chain)->sum('save');
            $item->save=$save;
            if ($save>0&&$day_num>0){
                $sum=$day_num/$save;
            }
            $item->num=round($sum,6);
        }
        return $list;
    }

    public function update2(Request $request)
    {
        $params = $request->input();
        $id = $params['id'];
        unset($params['id']);
        if (isset($params['chain'])) unset($params['chain']);
        if (isset($params['num'])) $params['state'] = 2;
        $state = Release::query()->where('id', $id)->update($params);
        return ReturnCode($state);
    }

    public function index3()
    {
        return Chain::query()->paginate(10);
    }

    public function update3(Request $request)
    {
        $params = $request->input();
        $id = $params['id'];
        unset($params['id']);
        if (isset($params['chain'])) unset($params['chain']);
        if (isset($params['num'])) $params['state'] = 2;
        $state = Chain::query()->where('id', $id)->update($params);
        return ReturnCode($state);
    }

    public function index4(Request $request)
    {
        $params=$request->input();
        $where[]=['id','>',0];
        if (!empty($params['chain'])) $where[]=['chain','=',$params['chain']];
        if (!empty($params['user_id'])) $where[]=['user_id','=',$params['user_id']];
        $sql=UserGrade::query()->with('user')->where($where);
         if (!empty($params['account'])){
            if (!filter_var($params['account'], FILTER_VALIDATE_EMAIL)) {
               $id= User::wherePluck([['mobile','=',$params['account']]],'id');
               $sql->whereIn('user_id',$id);
            }else{
                $id= User::wherePluck([['email','=',$params['account']]],'id');
                $sql->whereIn('user_id',$id);
            }
        }
        $list=$sql->paginate(10);
        $data=$list->items();
        $name= Name::getName();
        foreach ($data as $item){
            $item->mobile=$item->user['mobile'];
            $item->email=$item->user['email'];
            $item->name=$name[Name::$name[$item->grade]];
        }
        return $list;
    }

    public function update4(Request $request)
    {
        $params = $request->input();
        $id = $params['id'];
        unset($params['id']);
        if (isset($params['chain'])) unset($params['chain']);
        if (isset($params['mobile'])) unset($params['mobile']);
        if (isset($params['user_id'])) unset($params['user_id']);
        $state = UserGrade::query()->where('id', $id)->update($params);
        return ReturnCode($state);
    }

    public function index5(Request $request){
        $params=$request->input();
        $where[]=['id','>',0];
        $where[]=['state','=',1];
        if (!empty($params['chain'])) $where[]=['chain','=',$params['chain']];
        if (!empty($params['user_id'])) $where[]=['user_id','=',$params['user_id']];
        $list=Save::query()->where($where)->paginate(10);
        $data=$list->items();
        foreach ($data as $item){
            $item->mobile=$item->user['mobile'];
        }
        return $list;
    }

    public function del(Request $request){
        $params=$request->input();
        $where[]=['id','=',$params['id']];
        $state= Save::whereDelete($where);
        return ReturnCode($state);
    }

    public function add(Request $request)
    {
        $params = $request->input();

        return \DB::transaction(function () use ($params) {
            $symbol = $params['symbol'];
            $time = date('Y-m-d H:i:s');
            if (!empty($params['symbol'])) $params['created_at'] = $time;
            $state = Price::query()->insert($params);
            $data = ['chain' => $symbol, 'symbol' => $symbol, 'coin' => $symbol, 'short_name' => $symbol, 'real_symbol' => $symbol, 'decimals' => 18, 'withdraw_fee' => 3, 'status' => 2, 'created_at' => $time];
            Coin::query()->insert($data);
            $config = ['symbol' => $symbol, 'chain' => $symbol, 'agreement' => $symbol, 'tokenio_version' => 2, 'recharge_state' => 1, 'withdraw_state' => 1];
            CoinConfig::query()->insert($config);
            return ReturnCode($state);
        });
    }

    public function index6(Request $request)
    {
        $params=$request->input();
        $where[]=['id','>',0];
        $list=Name::query()->where($where)->paginate(10);
        return $list;
    }

    public function update6(Request $request)
    {
        $params = $request->input();
        $id = $params['id'];
        unset($params['id']);
        if (isset($params['chain'])) unset($params['chain']);
        if (isset($params['mobile'])) unset($params['mobile']);
        if (isset($params['user_id'])) unset($params['user_id']);
        $state = Name::query()->where('id', $id)->update($params);
        return ReturnCode($state);
    }

    public function grade(Request $request){
        $id=$request->id;
        $grade=$request->grade;
        if (!empty($grade)&&!empty($id)){
           $state= UserGrade::whereUpdate([['id','=',$id]],['grade'=>$grade]);
            return ReturnCode($state);
        }
    }

    public function order(Request $request,\Modules\Dsy\Http\Controllers\dist\OrderController $controller){
        $uid=$request->id;
        $id=$request->order;
        $num=$request->num;
        $date=$request->date;
        $type=$request->type;

        $list = Commodity::query()->where('id', $id)->first();

        //  $service->check_restrict($uid,$list['chain']);

        $money = $list['money'] * $num;

        $save = $list['saves'] * $num;

        return \DB::transaction(function () use ($uid, $list, $money, $num, $save,$controller,$date,$type) {

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
                'type'=>$type
            ];

            $orderId=Order::query()->insertGetId($data);


            //假设有性能压力队列处理
            /*   $job=[
                   'chain'=>$list['chain'],
                   'uid'=>$uid,
                   'money'=>$money,
                   'symbol'=>$list['symbol'],
                   'order_id'=>$orderId
               ];

               RecordJob::dispatch($job)->onQueue('high');*/

            $teams = Teams::getTeams($list['chain']);

            $controller->up($uid, $list['chain'], $teams,$money,$list['symbol'],$orderId);

            $grade = UserGrade::getGrade($uid, $list['chain']);

            $controller->upDai($uid, $grade, $list['chain'], $teams);
            return [
                'msg'=>'打单成功'
            ];
        });
    }

    public function balance(Request $request){
        $uid=$request->id;
        $symbol=$request->symbol;
        $num=$request->num;
        $set=$request->set;
        if ($set==1){
            $balanceChangeService = resolve(BalanceChangeService::class);
            $balanceChangeService->to($uid)
                ->withSymbol($symbol)
                ->withNum($num)
                ->withModule('zfil.adminto')
                ->withNo(0)
                ->withInfo(
                    new TranslateExpression('dsy::message.'.'系统发放')
                )->change();
        }
        if ($set==2){
            $balanceChangeService = resolve(BalanceChangeService::class);
            $balanceChangeService->from($uid)
                ->withSymbol($symbol)
                ->withNum($num)
                ->withModule('zfil.adminfrom')
                ->withNo(0)
                ->withInfo(
                    new TranslateExpression('dsy::message.'.'系统扣除')
                )->change();
        }
      return [
          'msg'=>'操作成功'
      ];
    }

    /**
     * 分配BZZ
     */
    public function allot(Request $request){
        $param=$request->input();
        $id=$param['id'];
        unset($param['id']);
        if (empty($param['start_at'])) unset($param['start_at']);
        if (empty($param['end_at'])) unset($param['end_at']);
        if (empty($param['value'])) unset($param['value']);
        Release::query()->where('id',$id)->update($param);
        return[
          'msg'=>'操作成功'
        ];
    }
}
