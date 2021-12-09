<?php


namespace Modules\Dsy\Http\Controllers\admin\api;


use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Dsy\Models\Teams;

class TeamController extends Controller
{

    public function index(){
    return   Teams::query()->paginate(10);
    }

    public function update(Request $request){
         $params= $request->input();
         $id=$params['id'];
         unset($params['id']);
        $state= Teams::query()->where('id',$id)->update($params);
        return ReturnCode($state);
    }
}
