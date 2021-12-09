<?php


namespace Modules\Dsy\Http\Controllers\admin\api;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Dsy\Models\Message;
use Modules\Dsy\Models\Test;


class TestController extends Controller
{
    public function edit(Request $request){
        $params=$request->input();
     $state=  Message::query()->update($params);
     return ReturnCode($state);
    }
}
