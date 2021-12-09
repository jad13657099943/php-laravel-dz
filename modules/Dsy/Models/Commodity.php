<?php


namespace Modules\Dsy\Models;


use Illuminate\Database\Eloquent\Model;

class Commodity extends Kernel
{
    public $table = 'commodity';
    public $guarded = [];

    //减库存
    public static function subtract($id){
        self::query()->where('id',$id)->decrement('residual_fraction',1);
    }
}
