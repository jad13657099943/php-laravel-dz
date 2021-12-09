<?php


namespace Modules\Dsy\Http\Controllers\admin;


use App\Http\Controllers\Controller;

class BalanceController extends Controller
{
    public function index(){
        return view('dsy::admin.balance.index');
    }
}
