<?php


namespace Modules\Dsy\Services;


use Modules\Dsy\Models\Dsy\Name;
use Modules\Dsy\Models\Order;
use Modules\Dsy\Models\User;
use Modules\Dsy\Models\UserGrade;

class DzService
{

    /**
     * 用户等级
     * @param $uid
     * @return mixed
     */
    public function grade($uid){
       $grade= UserGrade::query()->where('user_id',$uid)->value('grade');
       $list= Name::getName();
       return $list[Name::$name[$grade]];
    }

    /**
     * 用户团队人数
     * @param $uid
     * @return int
     */
    public function number($uid){
      return User::team($uid);
    }

    /**
     * 用户团队业绩
     * @param $uid
     * @return int|mixed
     */
    public function performance($uid){
        $uid = User::query()->where('inviter_id', $uid)->pluck('id');
        $num = Order::query()->whereIn('user_id', $uid)->sum('money');
        foreach ($uid as $datum) {
            $num = $num + $this->performance($datum);
        }
        return $num;
    }

    /**
     * 用户团队全部下级id
     * @param $uid
     * @return array
     */
    public function subordinate($uid){
        $uid = User::query()->where('inviter_id', $uid)->pluck('id')->toArray();
        foreach ($uid as $datum) {
            $uid = array_merge($uid ,$this->subordinate($datum));
        }
        return $uid;
    }

    /**
     * 用户团队列表
     * @param $uid
     * @param int $limit
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function teamList($uid,$limit=10){
        $uid=$this->subordinate($uid);
        $list= User::query()->whereIn('id',$uid)->with('grade')->orderBy('id','desc')->paginate($limit);
        $data= Name::getName();
        foreach ($list->items() as $item){
            $item->grade_text=$data[Name::$name[$item->grade['grade']]];
        }
        return $list;
    }

    /**
     * 用户直推列表
     * @param $uid
     * @param int $limit
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function directList($uid,$limit=10){
        $list= User::query()->where('inviter_id',$uid)->with('grade')->orderBy('id','desc')->paginate($limit);
        $data= Name::getName();
        foreach ($list->items() as $item){
            $item->grade_text=$data[Name::$name[$item->grade['grade']]];
        }
        return $list;
}
}
