<?php


namespace Modules\User\Http\Controllers\api;


use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Coin\Services\AssetService;
use Modules\User\Models\ProjectUser;
use Modules\User\Services\ProjectUserService;

class UserController extends Controller
{

    /**
     * 会员的扩展信息.
     *
     * @param Request $request
     * @return \Illuminate\Database\Eloquent\Model|mixed
     */
    public function info(Request $request)
    {
        $user = $request->user();
        $user['user_id']=$user['id'];
        return $user;
    }

    /**
     * 修改扩展信息.
     *
     * @param Request $request
     * @return string[]
     * @throws \Exception
     */
    public function editInfo(Request $request)
    {
        $param = [
            'header' => $request->input('header'),
            'nick_name' => $request->input('nick_name'),
        ];

        if ($param['nick_name']) {
            //注意中文字符长度
            if (strlen($param['nick_name']) < 2 || strlen($param['nick_name']) > 6) {
                throw new \Exception('昵称长度在2-6位字符之间');
            }
        }

        $user = $request->user();
        ProjectUser::query()->where('user_id', $user->id)
            ->update($param);
        $info = ProjectUser::query()->where('user_id', $user->id)->first();
        return $info;
    }

    /**
     * 会员下级
     * @param Request $request
     * @param ProjectUserService $service
     * @return mixed
     */
    public function userSons(Request $request, ProjectUserService $service)
    {
        $user = $request->user();
        $type = $request->input('type', 0);
        return $service->userSons($user->id, $type);
    }


    /**
     * 团队统计
     * @param Request $request
     * @param ProjectUserService $service
     * @return mixed
     */
    public function teamTotal(Request $request, ProjectUserService $service)
    {
        $user = $request->user();

        return $service->userTeamTotal($user->id);
    }



    /**
     * 获取币种余额.
     *
     * @param Request $request
     * @param AssetService $service
     * @return int[]
     */
    public function coinBalance(Request $request, AssetService $service)
    {
        $userId = $request->user()->id;
        $symbol = $request->input('symbol');
        $info = $service->one([
            'user_id' => $userId,
            'symbol' => $symbol,
        ], [
            'exception' => false,
        ]);

        $value = $info->balance ?? 0;

        return [
            'balance' => $value,
        ];
    }
}
