<?php


namespace Modules\Getcoin\Http\Controllers\api;


use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Getcoin\Http\Requests\LeaderInfoRequest;
use Modules\Getcoin\Models\UserLeader;

class LeaderInfoController extends Controller
{

    /**
     * 组长扩展信息
     */
    public function info(Request $request)
    {

        $userId = $request->input('leader_user_id');
        $info = UserLeader::query()->where('user_id', $userId)->first();
        return $info;
    }

    public function edit(LeaderInfoRequest $request)
    {

        $userId = $request->user()->id;
        $data = [
            'wechat' => $request->input('wechat'),
            'wechat_group' => $request->input('wechat_group'),
            'wechat_qrcode' => $request->input('wechat_qrcode'),
            'currency' => $request->input('currency'),
            'wallet_index' => $request->input('wallet_index'),
            'remark' => $request->input('remark'),
        ];

        $info = UserLeader::query()->where('user_id', $userId)->first();
        if ($info) {
            UserLeader::query()->where('id', $info->id)->update($data);
        } else {
            $model = new UserLeader($data);
            $model->save();
        }

        return ['msg' => '操作成功'];
    }

}
