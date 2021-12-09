<?php


namespace Modules\Dsy\Http\Controllers\admin\api;


use Illuminate\Http\Request;
use Modules\Dsy\Models\Banner;
use Modules\Dsy\Models\Commodity;
use Modules\Dsy\Models\Unite;

class BannerController
{
   public function index(){
        $list=Banner::query()->paginate(10);
        foreach ($list as $item){
            if ($item->type==1){
                $item->url=Commodity::whereValue([['id','=',$item->url]],'title');
            }
            if ($item->type==2){
                $item->url=Unite::whereValue([['id','=',$item->url]],'title');
            }
        }
        return $list;
   }
   public function add(Request $request){
       $params=$request->input();
       $id=$params['url'];
       if ($id>10000){
           $params['url']-=10000;
           $params['type']=2;
       }
       $params['created_at']=date('Y-m-d H:i:s');
       $state=Banner::query()->insert($params);
       return ReturnCode($state);
   }
   public function edit(Request $request){
       $params=$request->input();
       $id=$params['id'];
       unset($params['id']);
       if ($params['url']>10000){
           $params['url']-=10000;
           $params['type']=2;
       }
     $state=  Banner::query()->where('id',$id)->update($params);
       return ReturnCode($state);
   }
   public function del(Request $request){
       $params=$request->input();
       $state=  Banner::query()->where('id',$params['id'])->delete();
       return ReturnCode($state);
   }
}
