<?php


namespace Modules\Dsy\Http\Controllers\admin;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Dsy\Models\Commodity;
use Modules\Dsy\Models\Dsy\Chain;
use Modules\Dsy\Models\Dsy\Name;
use Modules\Dsy\Models\Dsy\Price;

class KernelController extends Controller
{

    public function index()
    {
        return view('dsy::admin.kernel.index');
    }

    public function index2()
    {
        return view('dsy::admin.kernel.index2');
    }

    public function index3()
    {
        return view('dsy::admin.kernel.index3');
    }

    public function index4()
    {
        $list=Chain::query()->pluck('chain');
        return view('dsy::admin.kernel.index4',['list'=>$list]);
    }
    public function index5()
    {
        $list=Chain::query()->pluck('chain');
        return view('dsy::admin.kernel.index5',['list'=>$list]);
    }

    public function index6()
    {
        return view('dsy::admin.kernel.index6');
    }

    public function grade(Request $request){
        $id=$request->id;
        $list= Name::getName();
        $data=[
          1=>$list['one'],
          2=>$list['a'],
          3=>$list['b'],
          4=>$list['c'],
          5=>$list['d'],
          6=>$list['e']
        ];
        return view('dsy::admin.kernel.edit',['list'=>$data,'id'=>$id]);
    }

    public function order(Request $request){
        $id=$request->id;
       $data= Commodity::query()->select('id','title')->get();
       foreach ($data as $datum){
           $list[$datum->id]=$datum->title;
       }
        return view('dsy::admin.kernel.order4',['list'=>$list,'id'=>$id]);
    }

    public function balance(Request $request){
        $id=$request->id;
        $data= Price::query()->distinct()->pluck('symbol');
        return view('dsy::admin.kernel.balance4',['list'=>$data,'id'=>$id]);
    }

}
