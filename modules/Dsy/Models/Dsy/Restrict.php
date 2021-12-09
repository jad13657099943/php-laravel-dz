<?php


namespace Modules\Dsy\Models\Dsy;


use Illuminate\Database\Eloquent\Model;

class Restrict extends Model
{
    public  $table='dsy_restrict';
    public  $guarded=[];

    public static function getRestrict($uid,$chain){
        $where[]=['user_id','=',$uid];
        $where[]=['chain','=',$chain];
        return self::query()->where($where)->value('state');
    }

    public static function  starRestrict($data){
        self::query()->insert($data);
    }
}
