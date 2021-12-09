<?php


namespace Modules\User\Http\Controllers\api;


use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\User\Http\Requests\SavePaymentRequest;
use Modules\User\Models\UserPayment;
use Modules\User\Services\PaymentService;

class PaymentController extends Controller
{

    public function list(Request $request)
    {

        $state = $request->input('state', 'all');
        $type = $request->input('type', 'all');
        $userId = $request->user()->id;

        $where[] = ['user_id', '=', $userId];
        if ($type != 'all') {
            $where[] = ['type', '=', $type];
        }
        if ($state != 'all') {
            $where[] = ['state', '=', $state];
        }

        $list = UserPayment::query()->where($where)
            ->orderBy('id', 'desc')
            ->paginate($request->limit);

        return $list;
    }


    public function info(Request $request)
    {

        $id = $request->input('id');
        $userId = $request->user()->id;
        $info = UserPayment::query()->where('id', $id)
            ->where('user_id', $userId)
            ->first();

        return $info;
    }


    //保存设置
    public function save(SavePaymentRequest $request)
    {

        $param = [
            'state' => $request->input('state', 1),
            'type' => $request->input('type'),
            'param' => $request->input('value'),
            'user_id' => $request->user()->id,
            'id' => $request->input('id', 0),
        ];

        $service = resolve(PaymentService::class);
        $service->savePayment($param);
        return ['msg' => '保存成功'];
    }


    public function delete(Request $request)
    {

        $id = $request->input('id');
        $userId = $request->user()->id;
        $info = UserPayment::query()->where('id', $id)
            ->where('user_id', $userId)
            ->first();
        if (empty($info)) {
            throw new \Exception('该信息不存在');
        }

        $info->delete();
        return ['msg' => '操作成功'];
    }

}
