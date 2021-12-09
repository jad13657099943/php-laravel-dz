<?php


namespace Modules\Dsy\Http\Controllers\admin;


use Illuminate\Routing\Controller;

class XianController extends Controller
{
    public function index(){
        return view('dsy::admin.xian.index');
    }
}
