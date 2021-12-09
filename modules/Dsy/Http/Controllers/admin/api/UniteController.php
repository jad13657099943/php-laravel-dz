<?php


namespace Modules\Dsy\Http\Controllers\admin\api;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Dsy\Models\Commodity;

use Modules\Dsy\Models\Order;
use Modules\Dsy\Models\Teams;
use Modules\Dsy\Models\Unite;
use Modules\Dsy\Models\UniteOrder;
use Modules\Dsy\Models\UniteTeams;

class UniteController extends Controller
{
    public function index(){
        return Unite::query()->paginate(10);
    }

    public function add(Request $request){
        $params=$request->input();
        $data=[
            'title'=>$params['title'],
            'img'=>$params['img'],
            'imgs'=>$params['imgs'],
            'period'=>$params['period'],
            'chain'=>$params['chain'],
            'price_text'=>$params['price_text'],
            'period_text'=>$params['period_text'],
            'start_time'=>$params['start_time'],
            'start_day'=>$params['start_day'],
            'bl'=>json_encode($params['bl']),
            'zhe'=>$params['zhe'],
            'created_at'=>date('Y-m-d H:i:s')
        ];
        $state= Unite::query()->insert($data);
        return ReturnCode($state);
    }

    public function del(Request $request){
        $params=$request->input();
        $state= Unite::query()->where('id',$params['id'])->delete();
        return ReturnCode($state);
    }
    public function edit(Request $request){
        $params=$request->input();
        $data=[
            'title'=>$params['title'],
            'img'=>$params['img'],
            'imgs'=>$params['imgs'],
            'chain'=>$params['chain'],
            'period'=>$params['period'],
            'price_text'=>$params['price_text'],
            'period_text'=>$params['period_text'],
            'start_time'=>$params['start_time'],
            'start_day'=>$params['start_day'],
            'bl'=>json_encode($params['bl']),
            'created_at'=>date('Y-m-d H:i:s'),
            'zhe'=>$params['zhe']
        ];
        $state= Unite::query()->where('id',$params['id'])->update($data);
        return ReturnCode($state);
    }

    public function index2(){
        return   UniteTeams::query()->paginate(10);
    }

    public function update(Request $request){
        $params= $request->input();
        $id=$params['id'];
        unset($params['id']);
        $state= UniteTeams::query()->where('id',$id)->update($params);
        return ReturnCode($state);
    }
    public function time(Request $request){
        $params = $request->input();
        if (empty($params['start_time'])) unset($params['start_time']);
        if (empty($params['end_time'])) unset($params['end_time']);
        $id=$params['id'];
        unset($params['id']);
        UniteOrder::query()->where('id',$id)->update($params);
        return [
            'code' => 200
        ];
    }
}
