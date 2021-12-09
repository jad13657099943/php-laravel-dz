<?php


namespace Modules\Dsy\Http\Controllers\admin;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Coin\Services\TokenioNoticeService;
use Modules\Dsy\Models\Message;
use Modules\Dsy\Models\Notice;
use Modules\Dsy\Models\Test;

class TestController extends Controller
{
    public function index()
    {
        $list= Message::getMessage('zhi');
        return view('dsy::admin.test.index',[
            'list'=>$list
        ]);
    }

    public function test(TokenioNoticeService $service,Request $request){
        $params=$request->input();
       $list=  Notice::query()->where('id',$params['id'])->first();
       $service->process($list);
    }
}
