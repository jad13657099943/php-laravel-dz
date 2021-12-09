<?php


namespace Modules\Dsy\Http\Controllers\admin\api;


use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Dsy\Models\Commodity;
use Modules\Dsy\Models\Dsy\Chain;
use Modules\Dsy\Models\Dsy\Save;
use Modules\Dsy\Models\Dsy\Total;
use Modules\Dsy\Models\Order;
use Modules\Dsy\Models\ProjectUser;
use Modules\Dsy\Models\Unite;
use Modules\Dsy\Models\UniteOrder;
use Modules\Dsy\Models\User;
use Modules\Dsy\Models\UserSy;
use Modules\Dsy\Models\UserSys;
use Modules\Dsy\Services\TreeService;
use Modules\Dsy\Services\UserService;

class UserController extends Controller
{

    public function tree(Request $request,TreeService $service)
    {


        $userId = $request->input('user_id');
        $where=[];
        if (!empty($userId)){
            $where[]=['inviter_id','=',$userId];
        }
        if (empty($userId)) {
            $userId=  $request->input('uid');

            if(is_numeric($userId)&&!preg_match("/^1[345678]{1}\d{9}$/",$userId)){
                $where[]= ['id','=',$userId];
            }

            if (filter_var($userId, FILTER_VALIDATE_EMAIL)) {
                $where[]=['email','=',$userId];

            }

            if (preg_match("/^1[345678]{1}\d{9}$/",$userId)){
                $where[]= ['mobile','=',$userId];
            }

            if (empty($userId)){
                $where[]=['inviter_id','=',0];
            }

        }

        /*$userList = $userService->all([
            'parent_id' => $userId,
        ], [
            'with' => 'user',
            'orderBy' => ['user_id', 'asc'],
            'exception' => false
        ]);*/
        $userList = User::query()->where($where)
            ->orderBy('id', 'asc')
            ->get();

        //$userService = resolve(UserService::class);
        $data = [];
        foreach ($userList as $item) {



            $msg = 'UID：' . $item->id .
                "、手机号：" . $item->mobile .
                "、 邮箱：" . $item->email.
                '、 入金：'.$service->earnings($item->id,'USDT','recharge_from').
                '、 USDT收益：'.$service->earnings($item->id,'USDT','extract').
                '、 BZZ收益：'.$service->earnings($item->id,'BZZ','redeem')
            ;

            /*$sonsNum = $userService->one([
                'parent_id' => $item->user_id,
            ], [
                'exception' => false
            ]);*/
            $sonsNum = User::query()->where('inviter_id', $item->id)->first();

            if ($sonsNum) {
                $isParent = true;
            } else {
                $isParent = false;
            }
            $data[] = [
                'user_id' => $item->id,
                'name' => $msg,
                'isParent' => $isParent
            ];
        }

        return ['code' => 200, 'data' => $data];
    }

    public function index(Request $request)
    {
        $where = [];
        $params = $request->input();
        if (!empty($params['id'])) $where[] = ['id', '=', $params['id']];
        if (!empty($params['account'])){
            if (!filter_var($params['account'], FILTER_VALIDATE_EMAIL)) {
              $where[]= ['mobile','=',$params['account']];
            }else{
                $where[]=['email','=',$params['account']];
            }
        }
        $sql = User::query()->where($where);
        $list = $sql->paginate(10);

        return $list;
    }

    public function setTime(Request $request)
    {
        $params = $request->input();
        $uidList = $params['id'];
        $chain = $params['chain'];
        $time = $params['date'];
        $where[] = ['chain', '=', $chain];
        if ($chain == 'UNITE') {
            $state = UniteOrder::query()->whereIn('user_id', $uidList)->where($where)->update(['start_time' => $time]);
        } else {
            $state = Order::query()->whereIn('user_id', $uidList)->where($where)->update(['start_time' => $time]);
        }
        return ReturnCode($state);
    }

    public function setSave(Request $request)
    {
        $params = $request->input();
        $uidList = $params['id'];
        $chain = $params['chain'];
        $save = $params['date'];
        $where[] = ['chain', '=', $chain];
        $where[] = ['zs', '>', 0];
        $time = date('Y-m-d H:i:s');
        if ($chain == 'UNITE') {
            $list = Unite::query()->first();
            $list['title']='前期认购';
            foreach ($uidList as $item) {
                $zs = UniteOrder::query()->where($where)->where('user_id', $item)->first();
                $id = [];
                $data = [];
                if (empty($zs)) {
                    $data = [
                        'user_id' => $item,
                        'num' => 1,
                        'save' => $save,
                        'money' => 0,
                        'chain' => $chain,
                        'content' => json_encode($list),
                        'zs' => 1,
                        'to_symbol' => $list['to_symbol'],
                        'start_time' => $time,
                        'end_time' => date('Y-m-d H:i:s', strtotime($time) + 540 * 86400),
                        'created_at' => $time
                    ];
                } else {
                    $id[] = $zs['id'];
                }
                UniteOrder::query()->insert($data);
                UniteOrder::query()->whereIn('id', $id)->update(['save' => \DB::raw('save + ' . $save)]);
            }

        } else {
            $list = Commodity::query()->where('chain', $chain)->first();
            foreach ($uidList as $item) {

                $zs = Order::query()->where($where)->where('user_id', $item)->first();

                $id = [];
                $data = [];
                if (empty($zs)) {
                    $data = [
                        'user_id' => $item,
                        'content' => json_encode(['title' => '前期认购']),
                        'num' => 1,
                        'save' => $save,
                        'money' => 0,
                        'need_fil' => 0,
                        'fil_state' => 0,
                        'chain' => $chain,
                        'bl' => 1,
                        'zs' => 1,
                        'to_symbol' => $list['to_symbol'],
                        'to_type' => $list['to_type'],
                        'start_time' => $time,
                        'end_time' => date('Y-m-d H:i:s', strtotime($time) + 540 * 86400),
                        'created_at' => $time
                    ];
                } else {
                    $id[] = $zs['id'];
                }
                Order::query()->insert($data);
                Order::query()->whereIn('id', $id)->update(['save' => \DB::raw('save + ' . $save)]);
            }
        }
        return ReturnCode(1);
    }

    public function setDaySave(Request $request)
    {
        $params = $request->input();
        $uidList = $params['id'];
        $chain = $params['chain'];
        $day = $params['date'];
        $max = $params['dates'];
        $time = date('Y-m-d H:i:s');
        $save = [];
        foreach ($uidList as $item) {
            $save[] = ['user_id' => $item, 'chain' => $chain, 'num' => $day, 'max' => $max, 'created_at' => $time];
        }
        Save::query()->insert($save);
        return ReturnCode(1);
    }

    //释放收益
    public function setSy(Request $request)
    {
        $params = $request->input();
        $time = date('Y-m-d H:i:s');
        $data[] = [
            'user_id' => $params['user_id'],
            'money' => $params['money'],
            'symbol' => Chain::$state[$params['chain']],
            'type' => '系统发放',
            'created_at' => $time
        ];
        $redeem = ['id' => $params['user_id'], 'symbol' => Chain::$state[$params['chain']], 'num' => $params['money'], 'mid' => 0, 'message' => '收益'];
        redeem($redeem);
        $state = Total::query()->insert($data);
        return ReturnCode($state);
    }

    //修改冻结
    public function setSys(Request $request)
    {
        $params = $request->input();
        $time = date('Y-m-d H:i:s');
        return \DB::transaction(function () use ($params, $time) {
            UserSys::query()->where('user_id', $params['user_id'])->where('chain', $params['chain'])->delete();
            $sys[] = ['user_id' => $params['user_id'],
                'order_id' => 555555,
                'money' => $params['money'],
                'symbol' => Chain::$state[$params['chain']],
                'type' => '每日挖矿(线性冻结)',
                'end_time' => date('Y-m-d H:i:s', strtotime('+180 day', time())),
                'created_at' => $time,
                'chain' => $params['chain']
            ];
            $state = UserSys::query()->insert($sys);
            return ReturnCode($state);
        });

    }

    //修改质押
    public function setZhi(Request $request)
    {
        $params = $request->input();
        //  $time = date('Y-m-d H:i:s');
        $chain = $params['chain'];
        $where[] = ['fil_state', '>', 0];
        $where[] = ['user_id', '=', $params['user_id']];
        //  $where[]=['end_time','>',$time];
        $where[] = ['chain', '=', $chain];
        $list = Order::query()->where($where)->get();

        $num = 0;
        $id = [];
        foreach ($list as $item) {
            $num += 1;
            $id[] = $item->id;
        }
        $zhi = 0;
        if ($params['money'] > 0 && $num > 0) {
            $zhi = $params['money'] / $num;
        }
        if (!empty($id)) {
            Order::query()->whereIn('id', $id)->update(['need_fil' => $zhi]);
        }
        if ($zhi <= 0) {
            foreach ($list as $item) {
                if ($item->start_time <= 0 || $item->end_time <= 0) {
                    Order::query()->where('id', $item->id)->update([
                        'start_time' => date('Y-m-d H:i:s', time() + 86400 * 15),
                        'end_time' => date('Y-m-d H:i:s', time() + 86400 * (15 + json_decode($item->content, true)['period'])),
                    ]);
                }
            }
        }
        return ReturnCode(1);
    }

    //删除用户
    public function del(Request $request){
        $params = $request->input();
        $state= User::query()->where('id',$params['id'])->delete();
        return ReturnCode($state);
    }

    //修改密码
    public function password(Request $request){
        $uid=$request->id;
        $password=$request->password;
        User::query()->where('id',$uid)->update(['password'=>HashMake($password)]);
        return[
            'msg'=>'修改成功'
        ];
    }

}
