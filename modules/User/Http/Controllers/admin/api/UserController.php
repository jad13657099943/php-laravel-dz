<?php


namespace Modules\User\Http\Controllers\admin\api;


use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Core\Services\Frontend\UserService;
use Modules\Getcoin\Models\UserLeader;
use Modules\User\Models\ProjectUser;
use Modules\User\Services\ProjectUserService;

class UserController extends Controller
{

    public function getWhereParam($request)
    {

        $where = [];
        if (!empty($request->id)) {
            $where[] = ['user_id', $request->id];
        }
        if (!empty($request->parent_id)) {
            $where[] = ['parent_id', $request->parent_id];
        }
        if (is_numeric($request->grade)) {
            $where[] = ['grade', $request->grade];
        }
        if (is_numeric($request->is_leader)) {
            $where[] = ['is_leader', $request->is_leader];
        }
        if (!empty($request->created_at)) {
            $time = explode('||', $request->created_at);
            $time[0] = date('Y-m-d H:i:s', strtotime($time[0]));
            $time[1] = date('Y-m-d H:i:s', strtotime($time[1]) + 86400);
            $where[] = ['created_at', '>=', [$time[0]]];
            $where[] = ['created_at', '<=', [$time[1]]];
        }
        $keyword = $request->keyword;
        if ($keyword) {
            $coreUserService = resolve(\Modules\Core\Services\Frontend\UserService::class);
            $userId = $coreUserService->query()
                ->where('mobile', 'like', '%' . $keyword . '%')
                ->orWhere('username', 'like', '%' . $keyword . '%')
                ->value('id');
            if ($userId) {
                $where[] = ['user_id', '=', $userId];
            } else {
                $where[] = ['user_id', '=', 0];
            }
        }

        return $where;
    }


    /**
     * Display a listing of the resource.
     * @param Request $request
     * @param AfilUserService $userService
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function index(Request $request)
    {

        $where = $this->getWhereParam($request);
        $result = ProjectUser::query()->where($where)
            ->with('user')
            ->orderBy('id', 'desc')
            ->paginate();


        $mainUserService = resolve(UserService::class);
        foreach ($result as $item) {
            $item->username = $item->user->username;
            $item->mobile = $item->user->mobile;
            //$item->grade_text = $item->GradeText;
            $item->leader_name = $item->is_leader;
            //释放激活
            $item->auth_text = $item->user->auth == 1 ? '已认证' : '未认证';

            //返回推荐人详情
            if ($item->parent_id > 0) {
                $parent = $mainUserService->getById($item->parent_id, ['exception' => false]);
                $parent->auth_text = $parent->auth == 1 ? '已实名' : '未实名';
            } else {
                $parent = '';
            }
            $item->parent = $parent;
        }
        return $result;
    }


    public function userEdit(Request $request)
    {

        $userId = $request->input('user_id');
        $isLeader = $request->input('is_leader', 0);

        $projectUser = ProjectUser::query()->where('user_id', $userId)->first();
        if ($isLeader != $projectUser->is_leader) {
            if ($isLeader == 1) {

                //成为组长，判断该会员上面会员不能有组长的存在
                $service = resolve(ProjectUserService::class);
                $pidAll = $service->getUserPidAll($userId);
                if ($pidAll) {

                    $haveLeader = ProjectUser::query()->whereIn('user_id', $pidAll)
                        ->where('is_leader', 1)
                        ->count();
                    if ($haveLeader) {
                        throw new \Exception('该会员上面会员有组长了，Ta不能设置为组长');
                    }
                }
                $projectUser->is_leader = 1;

                //写入组长信息表
                $userLeader = UserLeader::query()->where('user_id', $userId)->first();
                if (empty($userLeader)) {
                    $model = new UserLeader([
                        'user_id' => $userId
                    ]);
                    $model->save();
                }

            } else {
                //取消成为组长
                $projectUser->is_leader = 0;
            }
            $projectUser->save();
        }


        //$data['grade'] = $request->input('grade', 0);
        //ProjectUser::query()->where('user_id', $userId)->update($data);

        $password = $request->input('password');
        $payPassword = $request->input('pay_password');
        if ($password || $payPassword) {

            $coreUserService = resolve(UserService::class);
            $coreUser = $coreUserService->getById($userId);

            if ($password) {
                if (strlen($password) < 6) {
                    throw new \Exception('登录密码至少6位');
                }
                $coreUser->password = $password;
            }

            if ($payPassword) {
                if (strlen($payPassword) != 6 || !is_numeric($payPassword)) {
                    throw new \Exception('支付密码6位数字');
                }
                $coreUser->pay_password = $payPassword;
            }

            $coreUser->save();
        }

        //清空缓存
        $cacheKey = 'user:' . $userId;
        \Cache::tags($cacheKey)->flush();
        return ['msg' => '修改成功'];
    }


    public function tree(Request $request)
    {
        $userId = $request->input('user_id', 0);
        $uid = $request->input('uid', 0);
        if ($userId == 0 && $uid != 0) {
            $userId = $uid; //用于直接搜索用
        }
        if (empty($userId)) {
            $userId = $request->input('uid');
        }

        $parentInfo = '无推荐人';
        $user = User::query()->where('id', $userId)->first();
        if ($user && $user->inviter_id > 0) {
            $parent = User::query()->where('id', $user->inviter_id)->first();

            if ($parent) {
                $parentInfo = 'UID：' . $parent->id . '，会员名：' . $parent->username;
            }
        }

        $userList = ProjectUser::query()->where('parent_id', $userId)
            ->with('user')
            ->orderBy('user_id', 'asc')
            ->get();
        $data = [];
        foreach ($userList as $item) {
            $msg = 'UID：' . $item->user_id . "会员名：" . $item->user->username;

            $grade = $item->grade_text;
            $msg .= '、等级：' . $grade;

            $sonsNum = ProjectUser::query()->where('parent_id', $userId)->count();
            if ($sonsNum) {
                $isParent = true;
            } else {
                $isParent = false;
            }
            $data[] = [
                'user_id' => $item->user_id,
                'name' => $msg,
                'isParent' => $isParent
            ];
        }

        return ['code' => 200, 'data' => $data, 'user_id' => $userId, 'parent_info' => $parentInfo];
    }


}
