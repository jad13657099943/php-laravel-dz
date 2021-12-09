<?php


namespace Modules\Dsy\Http\Controllers\admin\api;


use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Hash;
use Modules\Dsy\Models\Admin;
use Modules\Dsy\Models\AdminU;


class AccountController extends Controller
{
    /**
     * 账户列表
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function index(){
        $list= AdminU::query()->paginate(10);
        $data=$list->items();
        foreach ($data as $item){
            $item->admin= Admin::query()->where('id',$item->admin_id)->value('name');
        }
        return $list;
    }

    /**
     * 添加
     * @param Request $request
     * @return array
     * @throws \Exception
     */
    public function add(Request $request){
        $params=$request->input();
        $isAdminU=AdminU::query()->where('username',$params['username'])->first();
        if (!empty($isAdminU)){
            throw new \Exception('账号唯一');
        }
        $params['password']=Hash::make($params['password']);
        $params['created_at']=date('Y-m-d H:i:s');
        $state=AdminU::query()->insert($params);
        return [
            'data'=>$state
        ];
    }

    /**
     * 删除
     * @param Request $request
     * @return bool[]
     */
    public  function del(Request $request){
        $params=$request->input();
        if ($params['id']!=1){
            AdminU::query()->where('id',$params['id'])->delete();
        }
        return [
            'data'=>true
        ];
    }

    /**
     * 编辑
     * @param Request $request
     * @return bool[]
     * @throws \Exception
     */
    public function edit(Request $request){
        $params=$request->input();
        $isAdminU=AdminU::query()->where('id','!=',$params['id'])->where('username',$params['username'])->first();
        if (!empty($isAdminU)){
            throw new \Exception('账号唯一');
        }
        if ($params['id']==1){
            throw new \Exception('无法操作该账号');
        }
        $id=$params['id'];
        if (!empty($params['password'])) {
            $params['password']=Hash::make($params['password']);
        }else{
            unset($params['password']);
        }
        if (empty($params['admin_id'])) unset($params['admin_id']);
        AdminU::query()->where('id',$id)->update($params);
        return [
            'data'=>true
        ];
    }
}
