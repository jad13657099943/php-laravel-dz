<?php


namespace Modules\Dsy\Http\Controllers\admin\api;




use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Dsy\Models\Order;
use Modules\Dsy\Services\OrderService;
use Modules\Dsy\Services\UserSyService;

class XianController extends Controller
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
            $id= \Modules\Zfil\Models\User::query()->where('username',$params['user'])->value('id');
            if (isset($id)) $params['user']=$id;
            $where[]=['user_id','=',$params['user']];
        }
        return $where;
    }

    public function index(UserSyService $service,Request $request){
        $params= $request->input();
        $where = $this->getWhereParam($params);
        $list= $service->paginate($where, [
            'orderBy' => ['id', 'desc'],
            'with' => ['user'],
        ]);
        foreach ($list as $item){
            $item->username=$item->user['username'];
        }
        return $list;
    }
}
