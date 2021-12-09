<?php


namespace Modules\Dsy\Http\Controllers\admin;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Dsy\Models\Commodity;
use Modules\Dsy\Models\Unite;

class UniteController extends Controller
{
    public function index(){
        return view('dsy::admin.unite.index');
    }

    public function index2(){
        return view('dsy::admin.unite.index2');
    }
    public function create(){
        return view('dsy::admin.unite.create');
    }
    public function edit(Request $request){
        $params=$request->input();
        $list= Unite::query()->where('id',$params['id'])->first();
        $list['imgs']=explode('|',$list['imgs']);
        $data=[];
        foreach ($list['imgs'] as $img){
            if (!empty($img)){
                $data[]=$img;
            }
        }
        $list['imgs']=$data;
        $list['bl']=json_decode($list['bl'],true);
        return view('dsy::admin.unite.edit',['list'=>$list]);
    }
}
