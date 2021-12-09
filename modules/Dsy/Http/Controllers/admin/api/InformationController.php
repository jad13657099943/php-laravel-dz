<?php


namespace Modules\Dsy\Http\Controllers\admin\api;



use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Dsy\Models\Information;

class InformationController extends Controller
{

    public function index()
    {
         return Information::query()->paginate(10);
    }

    public function add(Request $request){

         $params=$request->input();
         $data=[
             'title'=>$params['title'],
             'img'=>$params['img'],
             'content'=>$params['content'],
             'created_at'=>date('Y-m-d h:i:s')
         ];
        return Information::query()->insert($data);
    }

    public function del(Request $request){
        $params=$request->input();
        $state= Information::query()->where('id',$params['id'])->delete();
        return ReturnCode($state);
    }

    public function edit(Request $request){
        $params=$request->input();
        $id=$params['id'];
        $state=Information::query()->where('id',$id)->update($params);
        return ReturnCode($state);
    }

}

