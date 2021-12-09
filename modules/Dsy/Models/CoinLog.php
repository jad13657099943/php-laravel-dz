<?php


namespace Modules\Dsy\Models;


use Illuminate\Database\Eloquent\Model;

class CoinLog extends Model
{
    public $table = 'coin_log';
    public $guarded = [];

    public static function getBalance($uid,$where,$symbol){
        $where[]=['user_id','=',$uid];
        $where[]=['symbol','=',$symbol];
       return self::query()->where($where)->sum('num');
    }

    public static function getInBalance($uid,$where,$symbol,$action){
        $where[]=['user_id','=',$uid];
        $where[]=['symbol','=',$symbol];
        return self::query()->where($where)->whereIn('action',$action)->sum('num');
    }

    public static function getList($uid,$where,$symbol,$limit){
        $where[]=['user_id','=',$uid];
        $where[]=['symbol','=',$symbol];
        return self::query()->where($where)->paginate($limit);
    }

    public static function getNum($uid,$symbol,$action){
        $where[]=['user_id','=',$uid];
        $where[]=['symbol','=',$symbol];
        $where[]=['action','=',$action];
        return self::query()->where($where)->sum('num');
    }
}
