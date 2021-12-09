<?php


namespace Modules\Dsy\Models;


use Illuminate\Database\Eloquent\Model;

class UserSys extends Model
{
    public $table='usersys';
    public $guarded=[];

    public static function getUserSys($where){
        return self::query()->where($where)->select('id','money','order_id','symbol','user_id','chain')->get();
    }

    public static function getUid($where){
        return self::query()->where($where)->distinct()->pluck('user_id');
    }

    public static function getUserMoney($uid,$chain){
        return self::query()->where('user_id',$uid)->where('chain',$chain)->sum('money');
    }

}
