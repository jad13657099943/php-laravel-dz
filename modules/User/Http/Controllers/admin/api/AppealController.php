<?php


namespace Modules\User\Http\Controllers\admin\api;


use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\User\Services\AppealService;

class AppealController extends Controller
{
    public function index(Request $request, AppealService $service)
    {

        $where = [];
        if (isset($request->state)) {
            $where[] = ['state', $request->state];
        }
        $list = $service->paginate($where, [
            'orderBy' => ['id', 'desc'],
            'exception' => false,
            'with' => ['user']
        ]);
        foreach ($list as $item) {


            $item->user_info = $userInfo = $item->user->username;;

            $item->type_text = $item->type_text;
            $item->state_text = $item->state_text;
        }
        return $list;

    }


    public function editInfo(Request $request, AppealService $appealService)
    {

        $id = $request->input('id');
        $state = $request->input('state', 0);
        $reply = $request->input('reply');
        $info = $appealService->getById($id);
        $info->state = $state;
        $info->reply = $reply;
        $info->save();
        return ['msg' => '操作成功'];
    }
}
