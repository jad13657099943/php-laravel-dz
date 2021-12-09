<?php


namespace Modules\Dsy\Http\Controllers\admin;


use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Dsy\Models\Admin;
use Modules\Dsy\Models\Menus;

class AdminController extends Controller
{
    public function index(){
        return view('dsy::admin.admin.index');
    }

    public function add(){
        return view('dsy::admin.admin.add');
    }
    public function edit(Request $request){
        $params=$request->input();
        $list= Admin::query()->where('id',$params['id'])->first();
        return view('dsy::admin.admin.edit',[
            'list'=>$list
        ]);
    }
}
