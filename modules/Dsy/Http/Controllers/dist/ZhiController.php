<?php


namespace Modules\Dsy\Http\Controllers\dist;


use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

use Modules\Dsy\Models\CoinAsset;
use Modules\Dsy\Models\FilRecord;
use Modules\Dsy\Models\Message;
use Modules\Dsy\Models\Order;
use Modules\Dsy\Models\Release;
use QL\QueryList;

class ZhiController extends Controller
{





    //质押详情
    public function orderZhi(Request $request)
    {
        $params = $request->input();
        $list = Order::query()->where('id', $params['id'])->first();
        $lv = round($list['need_fil'] / $list['save'], 4);
        $save = $list['save'];
        $fil = $list['need_fil'];
        $to = $list['to_fil'];
        $zlv = $to / $fil;
        return [
            'list' => $list,
            'lv' => $lv,
            'save' => $save,
            'fil' => $fil,
            'to' => $to,
            'zlv' => $zlv,
            'dzy' => $fil - $to
        ];
    }

    //质押记录
    public function orderZhiList(Request $request)
    {
        $params = $request->input();
        if (empty($params['limit'])) $params['limit'] = 10;
        return FilRecord::query()->where('order_id', $params['id'])->paginate($params['limit']);
    }

    //质押数据详情
    public function setZhiDetail(Request $request)
    {
        $params = $request->input();
        $list = Order::query()->where('id', $params['id'])->first();
        $fil = $list['need_fil'];
        $to = $list['to_fil'];
        $zlv = $to / $fil;
        return [
            'fil' => $fil,
            'to' => $to,
            'zlv' => $zlv
        ];
    }

    //质押
    public function setZhi(Request $request)
    {
        $params = $request->input();
        $user = $request->user();
        return \DB::transaction(function () use ($params, $user) {

            $fil = CoinAsset::getFil($user['id']);
            $list = Order::query()->where('id', $params['id'])->first();
            if ($fil < $params['num']) throw new \Exception(trans("dsy::message.余额不足"));
            if ($params['num'] > $list['need_fil'] - $list['to_fil']) throw new \Exception(trans("dsy::message.超出需质押数"));
            $data = [
                'user_id' => $user['id'],
                'order_id' => $params['id'],
                'num' => $params['num'],
                'type' => '增加',
                'created_at' => date('Y-m-d H:i:s')
            ];
            FilRecord::query()->insert($data);
            $zhi_end_time = date('Y-m-d H:i:s', time() + 86400 * json_decode($list['content'], true)['pledge_day']);
            if ($list['start_time'] <= 0 || $list['end_time'] <= 0) {
                Order::query()->where('id', $params['id'])->update([
                    'to_fil' => \DB::raw('to_fil + ' . $params['num']),
                    'start_time' => date('Y-m-d H:i:s', time() + 86400 * 15),
                    'end_time' => date('Y-m-d H:i:s', time() + 86400 * (15 + json_decode($list['content'], true)['period'])),
                    'zhi_end_time' => $zhi_end_time
                ]);
            } else {
                Order::query()->where('id', $params['id'])->update([
                    'to_fil' => \DB::raw('to_fil + ' . $params['num']),
                    'zhi_end_time' => $zhi_end_time
                ]);
            }
            $free = ['id' => $user['id'], 'symbol' => 'FIL', 'num' => $params['num'], 'message' => 'dsy::message.质押扣除'];
            free($free);
            $order = Order::query()->where('id', $params['id'])->first();
            if ($order['to_fil'] >= $order['need_fil']) {
                Order::query()->where('id', $params['id'])->update(['fil_state' => 2]);
            }
        });
    }

    //质押数量
    public function num(Request $request)
    {
        $params = $request->input();
        $num = Order::query()->where('id', $params['id'])->value('to_fil');
        return [
            'data' => $num
        ];
    }

    //赎回
    public function redeem(Request $request)
    {
        $params = $request->input();
        $user = $request->user();
        \DB::transaction(function () use ($params, $user) {
            $list = Order::query()->where('id', $params['id'])->first();
            if ($params['num'] > $list['to_fil']) throw new \Exception(trans("dsy::message.可赎回数量不足"));
            $redeem = ['id' => $user['id'], 'symbol' => 'FIL', 'num' => $params['num'], 'message' => 'dsy::message.赎回质押'];
            redeem($redeem);
            $data = [
                'user_id' => $user['id'],
                'order_id' => $params['id'],
                'num' => $params['num'],
                'type' => '赎回',
                'created_at' => date('Y-m-d H:i:s')
            ];
            FilRecord::query()->insert($data);
            Order::query()->where('id', $params['id'])->update([
                'fil_state' => 1,
                'to_fil' => \DB::raw('to_fil - ' . $params['num'])
            ]);
        });
    }

    //我的总质押量
    public function user(Request $request)
    {
        $user = $request->user();
        $data = Order::query()->where('user_id', $user['id'])->sum('to_fil');
        return [
            'data' => $data
        ];
    }
}
