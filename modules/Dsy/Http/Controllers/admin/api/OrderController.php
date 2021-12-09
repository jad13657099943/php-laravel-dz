<?php


namespace Modules\Dsy\Http\Controllers\admin\api;


use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Dsy\Models\Order;
use Modules\Dsy\Models\UniteOrder;
use Modules\Dsy\Models\User;
use Modules\Dsy\Services\OrderService;

class OrderController extends Controller
{

    public function getWhereParam($params)
    {

        $where = [];
        if (!empty($params['created_at'])) {
            $time = explode('||', $params['created_at']);
            $time[0] = date('Y-m-d H:i:s', strtotime($time[0]));
            $time[1] = date('Y-m-d H:i:s', strtotime($time[1]) + 86400);
            $where[] = ['created_at', '>=', [$time[0]]];
            $where[] = ['created_at', '<=', [$time[1]]];
        }

        if (!empty($params['user'])) {
            $id = User::query()->where('username', $params['user'])->value('id');
            if (isset($id)) $params['user'] = $id;
            $where[] = ['user_id', '=', $params['user']];
        }
        if (!empty($params['chain'])) {
            $where[] = ['chain', '=', $params['chain']];
        }
        return $where;
    }

    public function index(OrderService $service, Request $request)
    {
        $params = $request->input();
        $where = $this->getWhereParam($params);
        $list = Order::query()->where($where)->with('user')->orderBy('audit_at','desc')->paginate($request->limit??10);
        foreach ($list as $item) {
            $item->username = $item->user['username'];
            $item->mobile = $item->user['mobile'];
            $item->email=$item->user['email'];
            $item->state = Order::$state[$item->state];
            $item->title = json_decode($item->content, true)['title'];
            $item->type_text=Order::$type[$item->type];
        }
        return $list;
    }

    public function add(Request $request)
    {
        $params = $request->input();
        $order = Order::query()->where('id', $params['id'])->first();
        $save = json_decode($order['content'], true)['saves'];
        return \DB::transaction(function () use ($save, $params) {
            Order::query()->where('id', $params['id'])->update([
                'save' => \DB::raw('save + ' . $save * $params['num']),
                'zs' => \DB::raw('zs + ' . $save * $params['num'])
            ]);
            return [
                'code' => 200
            ];
        });

    }

    public function del(Request $request)
    {
        $params = $request->input();
        Order::query()->where('id', $params['id'])->delete();
        return [
            'code' => 200
        ];
    }

    public function index2(Request $request)
    {
        $params = $request->input();
        $where = $this->getWhereParam($params);
        $where[] = ['id', '>', 0];
        $list = UniteOrder::query()->where($where)->with('user')->paginate(10);
        $data = $list->items();
        foreach ($data as $item) {
            $item->username = $item->user['username'];
            $item->mobile = $item->user['mobile'];
            $item->title = json_decode($item->content, true)['title'];
        }
        return $list;
    }

    public function time(Request $request){
        $params = $request->input();
        if (empty($params['start_time'])) unset($params['start_time']);
        if (empty($params['end_time'])) unset($params['end_time']);
        if (empty($params['created_at'])) unset($params['created_at']);
        $id=$params['id'];
        unset($params['id']);
        Order::query()->where('id',$id)->update($params);
        return [
            'code' => 200
        ];
    }
}
