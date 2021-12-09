<?php


namespace Modules\Dsy\Http\Controllers\admin;


use Illuminate\Routing\Controller;

use Modules\Dsy\Models\Order;
use Modules\Dsy\Models\Ttime;

class ReleaseController extends Controller
{
    public function index()
    {
        $list= Ttime::query()->first();
        $time = date('Y-m-d');
        $where[]=['start_time','<=',$time];
        $where[]=['end_time','>',$time];
        $save=Order::query()->where($where)->whereIn('fil_state',[0,2])->sum('save');
        return view('dsy::admin.release.index',[
            'list'=>$list,
            'save'=>$save
        ]);
    }
}
