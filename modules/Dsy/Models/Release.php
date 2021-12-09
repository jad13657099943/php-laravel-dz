<?php


namespace Modules\Dsy\Models;


use Illuminate\Database\Eloquent\Model;

class Release extends Model
{
    public $table='dsy_release';

    public $guarded=[];

    public  static function getRelease(){
        $list=self::query()->get();
        $data=[];
        foreach ($list as $item){
            $data[$item->chain]=$item->num;
        }
        return $data;
    }


}
