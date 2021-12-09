<?php


namespace Modules\Dsy\Http\Controllers\admin;


use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Dsy\Models\Dsy\Chain;
use Modules\Dsy\Models\Order;
use Modules\Dsy\Models\UserSy;
use Modules\Dsy\Models\UserSys;

class UserController extends Controller
{


    public function index(){
        $list=Chain::query()->pluck('chain');
        return view('dsy::admin.users.index',['list'=>$list]);
    }

    public function tree(Request $request)
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

        $sonNum = User::query()->where($where)->count();
        return view('dsy::admin.user.tree', [
            'user_id' => $userId,
            'son_num' => $sonNum,
        ]);
    }

    public function edit(Request $request){
        $params=$request->input();
        $id=$params['id'];
        $chain=$params['chain'];
        $where[]=['user_id','=',$id];
        $where[]=['chain','=',$chain];
        $ysf=UserSy::query()->where($where)->sum('money');
        $list=UserSys::query()->where($where)->where('end_time','>',date('Y-m-d H:i:s'))->select('end_time','money')->get();
        $time=time();
        $num=0;
        foreach ($list as $item){
           $num+=ceil((strtotime($item->end_time)-$time)/86400)*($item->money/180);
        }
        return view('dsy::admin.users.edit',
            ['id'=>$id,'num'=>$num,'num2'=>$ysf,'chain'=>$chain]
        );
    }
    public function edit2(Request $request){
        $params=$request->input();
        $id=$params['id'];
       // $time = date('Y-m-d H:i:s');
        $chain=$params['chain'];
        $where[]=['user_id','=',$id];
        $where[]=['chain','=',$chain];
        $where[]=['fil_state','>',0];
       // $where[]=['end_time','>',$time];
       $need= Order::query()->where($where)->sum('need_fil');
       $to= Order::query()->where($where)->sum('to_fil');
        return view('dsy::admin.users.edit2',
            ['id'=>$id,'need'=>$need,'to'=>$to,'chain'=>$chain]
        );
    }

}
