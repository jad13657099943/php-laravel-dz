<?php


namespace Modules\Dsy\Http\Controllers\admin\api;


use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Modules\Dsy\Models\Commodity;


class GoodController extends Controller
{
    public function index(Request $request)
    {
        $params = $request->input();
        $where[] = ['id', '>', 0];
        if (!empty($params['chain'])) $where[] = ['chain', '=', $params['chain']];
        return Commodity::query()->where($where)->paginate(10);
    }

    public function add(Request $request)
    {
        $params = $request->input();
        $params['created_at'] = date('Y-m-d H:i:s');
        $state = Commodity::query()->insert($params);
        return ReturnCode($state);
    }

    public function del(Request $request)
    {
        $params = $request->input();
        $state = Commodity::query()->where('id', $params['id'])->delete();
        return ReturnCode($state);
    }

    public function edit(Request $request)
    {
        $params = $request->input();
        $id = $params['id'];
        unset($params['id']);
        $state = Commodity::query()->where('id', $id)->update($params);
        return ReturnCode($state);
    }
}
