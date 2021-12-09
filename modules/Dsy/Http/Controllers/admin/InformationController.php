<?php


namespace Modules\Dsy\Http\Controllers\admin;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Modules\Dsy\Models\Information;


class InformationController extends Controller
{

    public function index()
    {
        return view('dsy::admin.informations.index');
    }
    public function create()
    {
        return view('dsy::admin.informations.create');
    }
    public function edit(Request $request){
        $params=$request->input();
       $list= Information::query()->where('id',$params['id'])->first();
        return view('dsy::admin.informations.edit',['list'=>$list]);
    }


}
