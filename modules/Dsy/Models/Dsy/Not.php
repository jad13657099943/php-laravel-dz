<?php


namespace Modules\Dsy\Models\Dsy;


use Modules\Dsy\Models\Kernel;

class Not extends Kernel
{
    public  $table='dsy_not';
    public  $guarded=[];

    public static function getNot($uid,$symbol){
       return self::query()->where('user_id',$uid)->where('symbol',$symbol)->value('money');
    }
}
