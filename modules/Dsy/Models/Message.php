<?php


namespace Modules\Dsy\Models;


use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    public $table = 'message';
    public $guarded = [];

    //值
    public static function getMessage($value){
       return self::query()->value($value);
    }

}
