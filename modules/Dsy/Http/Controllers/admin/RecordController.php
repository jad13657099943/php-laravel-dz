<?php


namespace Modules\Dsy\Http\Controllers\admin;


use App\Http\Controllers\Controller;
use Modules\Dsy\Models\Record;

class RecordController extends Controller
{

    public function index(){
        $list= Record::$type;
        return view('dsy::admin.record.index',['list'=>$list]);
    }
}
