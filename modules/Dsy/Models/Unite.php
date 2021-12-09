<?php


namespace Modules\Dsy\Models;




class Unite extends Kernel
{
    public $table='dsy_unite';

    public $guarded=[];

    public static function getUnite($id){

        return self::query()->where('id',$id)->first();

    }
}
