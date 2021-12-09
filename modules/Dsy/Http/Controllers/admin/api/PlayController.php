<?php


namespace Modules\Dsy\Http\Controllers\admin\api;


use App\Models\AdminUser;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Dsy\Models\Commodity;
use Modules\Dsy\Models\Dz\Play;
use Modules\Dsy\Models\Message;
use Modules\Dsy\Models\Order;
use Modules\Dsy\Models\Teams;
use Modules\Dsy\Models\User;
use Modules\Dsy\Models\UserGrade;
use Modules\Dsy\Services\OrderService;

class PlayController extends Controller
{

    /**
     * 提交打单
     * @param Request $request
     * @return string[]
     * @throws \Exception
     */
    public function order(Request $request)
    {
        $param = $request->input();
        $uid = $request->user()['id'];
        if (empty($uid)) throw new \Exception('身份过期，请刷新页面或重新登录');
        $model = new Play();
        $model->user_id = $param['user_id'];
        $model->admin_id = $uid;
        $model->commodity_id = $param['commodity_id'];
        $model->num = $param['num'];
        $model->buy_at = $param['buy_at'];
        $model->buy_type = $param['buy_type'];
        $model->save();
        return [
            'msg' => '提交成功'
        ];
    }

    public function getWhere($param)
    {
        $where = [];

        if (!empty($param['user'])) {
            $where[] = ['user_id', '=', $param['user']];
        }

        if (!empty($param['type'])) {
            $where[] = ['buy_type', '=', $param['type']];
        }

        if (!empty($param['created_at'])) {
            $time = explode('||', $param['created_at']);
            $time[0] = date('Y-m-d H:i:s', strtotime($time[0]));
            $time[1] = date('Y-m-d H:i:s', strtotime($time[1]) + 86400);
            $where[] = ['created_at', '>=', [$time[0]]];
            $where[] = ['created_at', '<=', [$time[1]]];
        }

        if (!empty($param['account'])) {
            if (!filter_var($param['account'], FILTER_VALIDATE_EMAIL)) {
                $id = User::whereValue([['mobile', '=', $param['account']]], 'id');
                $where[] = ['user_id', '=', $id];
            } else {
                $id = User::whereValue([['email', '=', $param['account']]], 'id');
                $where[] = ['user_id', '=', $id];
            }
        }
        if (!empty($param['state'])) {
            $where[] = ['state', '=', $param['state']];
        }
        return $where;
    }

    /**
     * 列表
     * @param Request $request
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function list(Request $request)
    {
        $where = $this->getWhere($request->input());
        $list = Play::query()->where($where)->with('user')->paginate($request->limit ?? 10);
        $data = AdminUser::query()->select('id', 'username')->get();
        $username = [];
        $username[3] = 'dzcw';
        foreach ($data as $datum) {
            $username[$datum->id] = $datum->username;
        }
        $username[0] = '暂无';
        foreach ($list->items() as $item) {
            if (isset($username[$item->admin_id])) {
                $item->admin_id = $username[$item->admin_id];
            }
            if (isset($username[$item->state_id])) {
                $item->state_id = $username[$item->state_id];
            }
            $item->mobile = $item->user['mobile'];
            $item->email = $item->user['email'];
            $item->state = Play::$state[$item->state];
            $item->buy_type = Order::$type[$item->buy_type];
        }
        return $list;
    }

    /**
     * 审核
     * @param Request $request
     * @param OrderService $service
     * @return mixed|string[]
     * @throws \Throwable
     */
    public function succeed(Request $request, OrderService $service)
    {
        $admin = $request->user()['id'];
        $model = Play::query()->where('id', $request->id)->first();
        if ($model['state'] != 1) return ['msg' => '已完成'];
        $id = $model['commodity_id'];
        $uid = $model['user_id'];
        $num = $model['num'];
        $date = $model['buy_at'];
        $type = $model['buy_type'];

        $list = Commodity::query()->where('id', $id)->first();

        //  $service->check_restrict($uid,$list['chain']);

        $money = $list['money'] * $num;

        $save = $list['saves'] * $num;

        return \DB::transaction(function () use ($uid, $list, $money, $num, $save, $service, $date, $type, $admin, $model) {

            $orderId = $service->createOrder($list, $save, $uid, $num, $money, $date, $type);

            //假设有性能压力队列处理
            /*   $job=[
                   'chain'=>$list['chain'],
                   'uid'=>$uid,
                   'money'=>$money,
                   'symbol'=>$list['symbol'],
                   'order_id'=>$orderId
               ];

               RecordJob::dispatch($job)->onQueue('high');*/

            $teams = Teams::getTeams($list['chain']);

            $service->up($uid, $list['chain'], $teams, $money, $list['symbol'], $orderId);

            $grade = UserGrade::getGrade($uid, $list['chain']);

            $service->upDai($uid, $grade, $list['chain'], $teams);

            $model->state = 2;
            $model->state_id = $admin;
            $model->save();

            return [
                'msg' => '打单成功'
            ];
        });
    }
}
