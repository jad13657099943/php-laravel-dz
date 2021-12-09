<?php


namespace Modules\zfil\Services\Traits;

use Illuminate\Database\Eloquent\Builder;
use Modules\Coin\Models\CoinAsset;

use Modules\zfil\Models\ProjectUser;
use Modules\zfil\Models\User;

use Modules\Core\Models\Frontend\UserInvitationTree;
use Modules\Core\Services\Frontend\UserInvitationService;
use Modules\Core\Services\Frontend\UserService;

trait UserTraits
{

    /**
     * 清除缓存
     * @param $userId
     */
    public function clearCacheByUserId($userId)
    {

        $cacheKey = 'user:' . $userId;
        \Cache::tags($cacheKey)->flush();
    }

    /**
     * 获取会员基础信息表数据
     * @param $userId
     * @return \App\Models\User
     */
    public function getUserBaseInfo($userId)
    {

        $userService = resolve(UserService::class);
        return $userService->getById(with_user_id($userId));
    }


    /**
     * 获取会员扩展表信息
     * @param $userId
     * @return \Illuminate\Database\Eloquent\Model|mixed
     */
    public function getUserExtendInfo($userId)
    {

        $userService = resolve(ProjectUserService::class);
        return $userService->one([
            'user_id' => with_user_id($userId)
        ]);
    }


    /**
     * 返回会员名
     * @param $userOrUserId
     * @param int $format
     * @return mixed|string
     */
    public function getUserName($userOrUserId, $format = 0)
    {

        $user = with_user($userOrUserId);
        $userName = '';
        if ($user->mobile) {
            $userName = $user->mobile;
        } elseif ($user->email) {
            $userName = $user->email;
        }
        if ($userName && $format == 1) {
            $userName = substr($userName, 0, 3) . '**' . substr($userName, -4, 4);
        }

        return $userName == '' ? $user->username : $userName;
    }

    /**
     * 获取会员直推会员列表
     * @param $userId
     * @return array|\Illuminate\Support\Collection
     */
    public function getUserSons($userId)
    {
        $service = resolve(ProjectUserService::class);
        $list = $service->all([
            'parent_id' => $userId,
        ], [
            'orderBy' => ['user_id', 'desc'],
            'exception' => false,
        ])->pluck('user_id')->toArray();

        return $list ?? [];
    }

    /**
     * 获取直推会员人数
     * @param $userId
     * @return int
     */
    public function getUserSonsNum($userId)
    {

        $service = resolve(ProjectUserService::class);
        $num = $service->count([
            'parent_id' => $userId
        ]);

        return $num ?? 0;
    }


    /**
     * 获取会员下面所有团队id
     * @param $userId
     */
    public function getUserTeam($userId)
    {

        $list = UserInvitationTree::whereJsonContains('data', $userId)->pluck('user_id')->toArray();
        return $list;
    }

    /**
     * 获取团队总人数
     * @return mixed
     */
    public function getUserTeamNum($userId)
    {

        $num = UserInvitationTree::whereJsonContains('data', $userId)->count();
        return $num ?? 0;
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


    /**
     * 更新会员扩展表信息
     * @param $userId
     * @param array $data
     * @return
     */
    public function updateProjectUserInfo($userId,$data = []){

        $userService = resolve(ProjectUserService::class);
        return $userService->model->where('user_id',$userId)->update($data);
    }

    //直推业绩
    public function belowPerformance($uId){
        $user=resolve(User::class);
        $idArray=$user::query()->where('inviter_id',$uId)->pluck('id');
         $order=resolve(DeviceOrder::class);
         return $order::query()->whereIn('user_id',$idArray)->sum('pay_num');
    }
    //团队业绩
    public function teamPerformance($uId){

        $lists= ProjectUser::query()->where('parent_id',$uId)->pluck('user_id');
        $data=DeviceOrder::query()->whereIn('user_id',$lists)->sum('pay_num');
        foreach ($lists as $item){
            $data=$data+$this->teamPerformance($item);
        };
        return $data;
    }
    //src余额
    public function src($uId){
       return round(CoinAsset::query()->where('user_id',$uId)->where('symbol','SRC')->value('balance'),4);
    }
    //cnb余额
    public function cnb($uId){
        $balance=CoinAsset::query()->where('user_id',$uId)->where('symbol','CNB')->value('balance');
        $money= ProjectUser::query()->where('user_id',$uId)->select('tiyan_points','reward')->first();
        return round($balance+$money['tiyan_points']+$money['reward'],4);
    }
}
