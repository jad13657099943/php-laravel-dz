<?php


namespace Modules\Dsy\Models\Dsy;



use Modules\Dsy\Models\Kernel;

class Price extends Kernel
{
    public  $table='dsy_price';

    public $guarded=[];

    public static function getPrice($symbol){
        return self::query()->where('symbol',$symbol)->value('price');
    }

    public static function getFree($symbol){
        return self::query()->where('symbol',$symbol)->value('free');
    }



}
