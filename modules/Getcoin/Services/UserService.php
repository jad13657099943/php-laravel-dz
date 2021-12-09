<?php


namespace Modules\Getcoin\Services;


use Modules\User\Models\ProjectUser;
use Modules\User\Services\ProjectUserService;

class UserService
{

    /**
     * 获取会员所属的组长
     */
    public function myLeader($userId)
    {

        $service = resolve(ProjectUserService::class);
        $pidAll = $service->getUserPidAll($userId);
        $pidAll[] = $userId; //自己是组长直接会先返回自己

        $leaderUserId = ProjectUser::query()->where('is_leader', 1)
                ->whereIn('user_id', $pidAll)
                ->orderBy('user_id', 'desc')
                ->limit(1)
                ->value('user_id') ?? 0;
        return $leaderUserId;
    }

}
