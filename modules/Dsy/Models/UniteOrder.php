<?php


namespace Modules\Dsy\Models;


use Hamcrest\Core\SampleBaseClass;
use Illuminate\Database\Eloquent\Model;

class UniteOrder extends Kernel
{
    public $table='dsy_unite_order';

    public $guarded=[];
    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }
    public static function getUniteOrder($where){
       return self::query()->where($where)->select('id','user_id','money','save','content','chain','to_symbol')->get();
    }
}
