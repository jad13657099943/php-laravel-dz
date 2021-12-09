<?php


namespace Modules\Dsy\Models\Dsy;


use Modules\Dsy\Models\Kernel;

class Total extends Kernel
{
    public  $table='dsy_total';
    public  $guarded=[];

    public static function getList($where,$limit){

        return self::query()->where($where)->orderBy('id','desc')->paginate($limit);
    }
}
