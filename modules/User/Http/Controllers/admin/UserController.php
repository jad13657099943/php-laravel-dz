<?php


namespace Modules\User\Http\Controllers\admin;


use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Core\Models\Frontend\UserInvitationTree;
use Modules\User\Models\ProjectUser;

class UserController extends Controller
{

    public function index()
    {
        return view('user::admin.user.index', [
            'grade_list' => ProjectUser::$gradeMap,
            'leader'=>ProjectUser::$leaderNameMap
        ]);
    }


    public function userEdit(Request $request)
    {

        $userId = $request->input('user_id');
        $info = ProjectUser::query()->where('user_id', $userId)->first();
        return view('user::admin.user.edit', [
            'info' => $info,
            'grade_list' => ProjectUser::$gradeMap,
        ]);
    }


    public function tree(Request $request)
    {
        $userId = $request->input('user_id', 0);
        $sonNum = ProjectUser::query()->where('parent_id', $userId)->count();
        return view('user::admin.user.tree', [
            'user_id' => $userId,
            'son_num' => $sonNum,
            'uid' => $userId,
        ]);
    }


    /**
     * 获取所有上级
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function parentAll(Request $request)
    {

        $id = $request->input('id', 0);
        $tree = UserInvitationTree::query()->where('user_id', $id)
            ->value('data');
        $parentList = [];
        if ($tree) {

            foreach ($tree as $item) {
                $info = User::query()->where('id', $item)
                    ->select("id", "username", "mobile", "email", "created_at")
                    ->first();
                $parentList[] = $info;
            }
        }

        $user = User::query()->where('id', $id)->first();
        if (empty($user)) {
            $user = User::query()->newModelInstance();
        }
        return view('user::admin.user.parent', [
            'data' => $parentList,
            'user' => $user,
            'count'=>count($parentList)
        ]);
    }


}
