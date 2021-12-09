<?php


namespace Modules\Dsy\Http\Controllers\admin;


use Illuminate\Routing\Controller;
use Modules\Dsy\Models\Order;
use Modules\Dsy\Models\Teams;
use Modules\Dsy\Models\Ttime;

class TeamController extends Controller
{
    public function index()
    {
        return view('dsy::admin.team.index');
    }
}
