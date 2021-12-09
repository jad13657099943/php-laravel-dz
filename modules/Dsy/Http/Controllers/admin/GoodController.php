<?php


namespace Modules\Dsy\Http\Controllers\admin;




use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Dsy\Models\Commodity;
use Modules\Dsy\Models\Dsy\Chain;
use Modules\Dsy\Models\Dsy\Price;

class GoodController extends Controller
{
     public function index(){
         $list=Chain::query()->where('chain','!=','UNITE')->pluck('chain');
         return view('dsy::admin.good.index',[
             'list'=>$list
         ]);
     }

     public function create(){
         $list=Chain::query()->where('chain','!=','UNITE')->pluck('chain');
         $lists=Price::query()->pluck('symbol');
         return view('dsy::admin.good.create',[
             'list'=>$list,
             'lists'=>$lists
         ]);
     }

     public function edit(Request $request){
         $params=$request->input();
         $list2=Chain::query()->where('chain','!=','UNITE')->pluck('chain');
         $list3=Price::query()->pluck('symbol');
         $list= Commodity::query()->where('id',$params['id'])->first();
         $list['imgs']=explode('|',$list['imgs']);
         $data=[];
         foreach ($list['imgs'] as $img){
            if (!empty($img)){
                $data[]=$img;
            }
         }
         $list['imgs']=$data;
         $datas=[1=>'基础+线性',2=>'线性',3=>'无规则'];
         $text=$datas[$list->to_type];
        return view('dsy::admin.good.edit',['list'=>$list,'list2'=>$list2,'list3'=>$list3,'text'=>$text]);
     }
}
