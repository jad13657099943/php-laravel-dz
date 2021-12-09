<?php


namespace Modules\Getcoin\Http\Controllers\api;


use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Getcoin\Models\AccountSetting;
use Modules\Getcoin\Models\UserBindAccount;

/* 绑定第三方账户 */

class AccountController extends Controller
{

    /**
     * 会员绑定的钱包地址列表
     * @param Request $request
     * @return \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public function list(Request $request)
    {

        $userId = $request->user()->id;
        $list = AccountSetting::query()->where('state', 1)
            ->select('name')
            ->get();
        foreach ($list as $item) {
            $info = UserBindAccount::query()->where('user_id', $userId)
                ->where('name', $item->name)
                ->first();
            $item->account = $info->account ?? '';
            $item->id = $info->id ?? 0;
        }

        return $list;
    }


    public function info(Request $request)
    {

        $name = $request->input('name');
        $userId = $request->user()->id;
        $info = UserBindAccount::query()->where('user_id', $userId)
            ->where('name', $name)
            ->first();

        return $info;
    }

    /**
     * 绑定账户
     */
    public function save(Request $request)
    {

        $name = $request->input('name');
        $userId = $request->user()->id;
        $account = $request->input('account');
        $param = $request->input('param');
        $id = $request->input('id');

        if ($id) {
            $model = UserBindAccount::query()->find($id);
            $model->account = $account;
            $model->param = $param;
            $model->save();
        } else {

            $model = new UserBindAccount([
                'user_id' => $userId,
                'name' => $name,
                'account' => $account,
                'param' => $param
            ]);

            $model->save();
        }

        return $model;
    }

}
