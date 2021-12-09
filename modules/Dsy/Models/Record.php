<?php


namespace Modules\Dsy\Models;


use Illuminate\Database\Eloquent\Model;

class Record extends Model
{
    public $table = 'record';
    public $guarded = [];

    public static $type=[
      1=>'直推奖励',
      2=>'级差奖励',
      3=>'直推分公司奖励',
      4=>'合伙人全球销售分红'
    ];

    public static function getList($where,$limit){
        return self::query()->where($where)->orderBy('id','desc')->paginate($limit);
    }

    public function user(){
        return $this->hasOne(User::class,'id','user_id');
    }

    public function userTo(){
        return $this->hasOne(User::class,'id','users');
    }

}
