<?php


namespace Modules\Coin\Http\Controllers\Admin\Api;


use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Coin\Services\UserWalletService;
use Modules\Core\Services\Frontend\UserService;

class UserWalletController extends Controller
{

    public function index(Request $request, UserWalletService $service)
    {


        //查询参数
        $where = [];
        $param = $request->input('key');
        if (!empty($param['user_info'])) {

            $userService = resolve(UserService::class);
            $userInfo = $userService->one(['id' => $param['user_info']], [
                'exception' => false,
                'queryCallback' => function ($query) use ($param) {
                    $query->orWhere('username', $param['user_info'])->value('id');
                }
            ]);

            if (!$userInfo) {
                $userId = -1;
            } else {
                $userId = $userInfo->id;
            }
            $where[] = ['user_id', '=', $userId];
        }

        $list = $service->paginate($where,[
            'with'=>'user'
        ]);

        foreach ($list as $item) {
            $item->user_name = $item->user['username'];
        }

        return $list;
    }

}
