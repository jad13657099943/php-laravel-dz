<?php


namespace Modules\Dsy\Models;


use Illuminate\Database\Eloquent\Model;

class UserSy extends  Kernel
{
    public $table = 'usersy';
    public $guarded = [];

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public static function getUserSy($where){
        return self::query()->where($where)->select('id','money','order_id','symbol','user_id','chain')->get();
    }

    public static function getList($where,$limit){

        return self::query()->where($where)->orderBy('id','desc')->paginate($limit);
    }

    public static function getUser($where){
        return self::query()->where($where)->selectRaw('sum(money) as money ,symbol,chain,user_id')->groupBy('user_id')->get();
    }

    public static function getOrderId($where){
        return self::query()->where($where)->pluck('order_id')->toArray();
    }

}
