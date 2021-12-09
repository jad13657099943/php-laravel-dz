<?php


namespace Modules\Dsy\Http\Controllers\admin\api;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Dsy\Models\Order;
use Modules\Dsy\Models\Record;
use Modules\Dsy\Models\User;

class RecordController extends Controller
{

    public function getWhere($param){
        $where=[];

        if (!empty($param['user'])){
            $where[]=['user_id','=',$param['user']];
        }

        if (!empty($param['type'])){
            $where[]=['type','=',$param['type']];
        }

        if (!empty($param['created_at'])) {
            $time = explode('||', $param['created_at']);
            $time[0] = date('Y-m-d H:i:s', strtotime($time[0]));
            $time[1] = date('Y-m-d H:i:s', strtotime($time[1]) + 86400);
            $where[] = ['created_at', '>=', [$time[0]]];
            $where[] = ['created_at', '<=', [$time[1]]];
        }

        if (!empty($param['account'])){
            if (!filter_var($param['account'], FILTER_VALIDATE_EMAIL)) {
                $id= User::whereValue([['mobile','=',$param['account']]],'id');
                $where[]=['user_id','=',$id];
            }else{
                $id= User::whereValue([['email','=',$param['account']]],'id');
                $where[]=['user_id','=',$id];
            }
        }
       return $where;
    }

    public function list(Request $request){

        $where=$this->getWhere($request->input());
        $list=Record::query()->where($where)->with('user','userTo')->orderBy('id','desc')->paginate($request->limit??10);
        foreach ($list->items() as $item){
            $item->mobile=$item->user['mobile'];
            $item->email=$item->user['email'];
            $item->bl=($item->bl*100).'%';
            if (!empty($item->userTo)){
                $item->mobile2=$item->userTo['mobile'];
                $item->email2=$item->userTo['email'];
            }else{
                $item->mobile2='';
                $item->email2='';
            }

        }
        return $list;
    }
}
