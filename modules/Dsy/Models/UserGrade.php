<?php


namespace Modules\Dsy\Models;




class UserGrade extends Kernel
{
    public $table='dsy_user_grade';
    public $guarded=[];




    //获取等级
    public static function getGrade($uid,$chain){
       return self::query()->where('user_id',$uid)->where('chain',$chain)->value('grade');
    }

    //提升等级
    public static function setGrade($uid,$chain,$grade){
        self::query()->where('user_id',$uid)->where('chain',$chain)->update(['grade'=>$grade]);
    }

    //初始等级
    public static function starGrade($data){
        self::query()->insert($data);
    }

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

}
