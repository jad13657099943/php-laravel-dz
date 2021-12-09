<?php


namespace Modules\User\Services;


use Modules\Core\Models\Frontend\UserInvitationTree;
use Modules\Core\Services\Frontend\UserInvitationService;
use Modules\User\Models\ProjectUser;

class ProjectUserService
{

    /**
     * 会员团队列表.
     * @param $userId
     * @return mixed
     */
    public function userSons($userId)
    {


        $where[] = ['parent_id', '=', $userId];
        $list = ProjectUser::query()->where($where)
            ->with('user')
            ->orderBy('id', 'desc')
            ->paginate();
        foreach ($list as $item) {

            $username = $item->user->username;
            $item->user_yeji = 0;
            $item->username = $username;
            $item->total = 0;

        }
        return $list;
    }


    /**
     * 团队页面统计
     * @param $userId
     * @return array
     */
    public function userTeamTotal($userId)
    {


        $user = ProjectUser::query()->where('user_id', $userId)->first();
        $user->grade_text = $user->grade_text;

        //我的直推总人数
        $sonsNum = ProjectUser::query()->where('parent_id', $userId)->count();
        //团队总人数
        $teamUser = $this->getTeamUserIds($userId);
        //团队总业绩
        $teamYeji = 0;
        //新增人数
        $addTeamNum = UserInvitationTree::whereJsonContains('data', $userId)
            ->where('created_at', '>=', date('Y-m-d'))
            ->pluck('user_id')->toArray();
        //新增业绩
        $addYejiNum = 0;

        return [

            'sons_num' => $sonsNum,
            'team_num' => count($teamUser),
            'team_yeji' => floatval($teamYeji),
            'add_team_num' => count($addTeamNum),
            'add_team_yeji' => floatval($addYejiNum),
            'user' => $user,
        ];

    }


    /**
     * 获取会员下面所有团队id
     * @param $userId
     */
    public function getTeamUserIds($userId)
    {

        $list = UserInvitationTree::whereJsonContains('data', $userId)->pluck('user_id')->toArray();
        return $list;
    }

    /**
     * 获取所有直推会员id
     * @param $userId
     * @return array
     */
    public function getUserSonIds($userId)
    {

        return ProjectUser::query()->where('parent_id', $userId)->pluck('user_id')->toArray();
    }

    /**
     * 获取会员所有上级ID
     * @param $userId
     * @return array
     */
    public function getUserPidAll($userId)
    {


        $userInvitationService = resolve(UserInvitationService::class);
        $team = $userInvitationService->getInvitersByUser($userId);
        $userIds = [];
        if ($team) {
            foreach ($team as $item) {
                $userIds[] = $item->id;
            }
            //一定要倒序输出，因为数据表存的关系是相反的
            $userIds = array_reverse($userIds);
        }

        return $userIds;
    }

}
