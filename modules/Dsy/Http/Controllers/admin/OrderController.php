<?php


namespace Modules\Dsy\Http\Controllers\admin;


use Illuminate\Routing\Controller;
use Modules\Dsy\Models\Dsy\Chain;
use Modules\Dsy\Models\Order;

class OrderController extends Controller
{
    public function index(){

       $list= Order::query()->selectRaw('sum(save) as save, sum(money) as sum_money')->get()->toArray();
        return view('dsy::admin.order.index',[
            'save'=>$list[0]['save'],
            'sum_money'=>$list[0]['sum_money']
        ]);
    }

    public function index2(){
        return view('dsy::admin.uniteOrder.index');
    }
}
