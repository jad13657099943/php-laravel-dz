<?php


namespace Modules\Dsy\Http\Controllers\admin;



use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Dsy\Models\Commodity;
use Modules\Dsy\Models\Dz\Play;
use Modules\Dsy\Models\Order;

class PlayController extends Controller
{
    public function index(){
        return view('dsy::admin.play.index');
    }

    public function order(Request $request){
        $id=$request->id;
        $data= Commodity::query()->select('id','title')->get();
        foreach ($data as $datum){
            $list[$datum->id]=$datum->title;
        }
        $data=Order::$type;
        unset($data['1']);
        return view('dsy::admin.play.order',['list'=>$list,'id'=>$id,'data'=>$data]);
    }

    public function list(){
        $data=Order::$type;
        unset($data['1']);
        $state=Play::$state;
        return view('dsy::admin.play.list',['list'=>$data,'state'=>$state]);
    }
}
