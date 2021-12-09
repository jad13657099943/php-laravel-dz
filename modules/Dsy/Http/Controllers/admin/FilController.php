<?php


namespace Modules\Dsy\Http\Controllers\admin;


use Illuminate\Routing\Controller;

class FilController extends  Controller
{
    public function index(){
        return view('dsy::admin.fil.index');
    }
}
