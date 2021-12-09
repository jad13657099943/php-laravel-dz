<?php


namespace Modules\Dsy\Models;


use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    public $table = 'team';
    public $guarded = [];

    public static function getList($where,$limit){
        return self::query()->where($where)->orderBy('id','desc')->paginate($limit);
    }
}
