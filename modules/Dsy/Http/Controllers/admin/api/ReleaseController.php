<?php


namespace Modules\Dsy\Http\Controllers\admin\api;


use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Dsy\Models\Ttime;

class ReleaseController extends Controller
{
    public function edit(Request $request){
        $params=$request->input();
        $state=  Ttime::query()->update($params);
        return ReturnCode($state);
    }
}
