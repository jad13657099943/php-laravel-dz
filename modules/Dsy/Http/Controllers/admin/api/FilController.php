<?php


namespace Modules\Dsy\Http\Controllers\admin\api;


use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Dsy\Models\Order;
use Modules\Dsy\Models\ProjectUser;
use Modules\Dsy\Models\User;

class FilController extends Controller
{
    public function getWhereParam($params)
    {

        $where = [];
        if (!empty($params['created_at'])) {
            $time = explode('||', $params['created_at']);
            $time[0] = date('Y-m-d H:i:s', strtotime($time[0]));
            $time[1] = date('Y-m-d H:i:s', strtotime($time[1]) + 86400);
            $where[] = ['created_at', '>=', [$time[0]]];
            $where[] = ['created_at', '<=', [$time[1]]];
        }

        if (!empty($params['user'])){
            $id= User::query()->where('username',$params['user'])->value('id');
            if (isset($id)) $params['user']=$id;
            $where[]=['user_id','=',$params['user']];
        }
        return $where;
    }
    public function index(Request $request){
        $params= $request->input();
        $where = $this->getWhereParam($params);
        $time = date('Y-m-d');
        $where[] = ['start_time', '<=', $time];
        $where[] = ['end_time', '>', $time];
        $list = Order::query()->where($where)->whereIn('fil_state', [0, 2])->paginate(10);
        $data=$list->items();
        foreach ($data as $datum){
           $user= User::query()->where('id',$datum->user_id)->select('username','mobile')->first();
            $datum->username=$user['username'];
            $datum->mobile=$user['mobile'];
            $datum->title=json_decode($datum->content,true)['title'];
        }
        return $list;
    }
}
