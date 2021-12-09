<?php


namespace Modules\Dsy\Http\Controllers\admin;


use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Dsy\Models\Banner;
use Modules\Dsy\Models\Commodity;
use Modules\Dsy\Models\Unite;

class BannerController extends  Controller
{
    public function index(){
        return view('dsy::admin.banner.index');
    }

    public function create(){
        $list= Commodity::query()->select('id','title')->get();
        $lists=Unite::query()->select('id','title')->get();
        foreach ($lists as $item){
            $item->id+=10000;
        }
        return view('dsy::admin.banner.create',[
            'list'=>$list,
            'lists'=>$lists
        ]);
    }

    public function edit(Request $request){
        $params=$request->input();
        $list= Banner::query()->where('id',$params['id'])->first();
        if ($list['type']==1){
          $list['url']=Commodity::query()->where('id',$list['url'])->value('title');
        }
        if ($list['type']==2){
            $list['url']=Unite::query()->where('id',$list['url'])->value('title');
        }
        return view('dsy::admin.banner.edit',['list'=>$list]);
    }
}
