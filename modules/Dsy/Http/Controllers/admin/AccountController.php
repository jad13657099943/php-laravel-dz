<?php


namespace Modules\Dsy\Http\Controllers\admin;


use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Dsy\Models\Admin;
use Modules\Dsy\Models\AdminU;

class AccountController extends Controller
{
    public function index(){
        return view('dsy::admin.account.index');
    }

    public function add(){
        $list= Admin::query()->select('id','name')->get();
        return view('dsy::admin.account.add',[
            'list'=>$list
        ]);
    }

    public function edit(Request $request){
        $list=Admin::query()->select('id','name')->get();
        $params= $request->input();
        $user= AdminU::query()->where('id',$params['id'])->first();
        $admin=[];
        $id=[];
        foreach ($list as $item){
            $admin[$item->id]=$item->name;
            $id[]=$item->id;
        }
        $name='';
        if (isset($admin)&&isset($user['admin_id'])&&in_array($user['admin_id'],$id)) $name=$admin[$user['admin_id']];
        return view('dsy::admin.account.edit',['list'=>$list, 'user'=>$user, 'name'=>$name]);
    }
}
