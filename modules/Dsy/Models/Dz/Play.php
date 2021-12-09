<?php


namespace Modules\Dsy\Models\Dz;


use Illuminate\Database\Eloquent\Model;
use Modules\Dsy\Models\User;

class Play extends Model
{
    public $table='dz_play';

    public $guarded=[];

    public static $state=[
      1=>'未审核',
      2=>'已审核'
    ];

    public function user(){
        return $this->hasOne(User::class,'id','user_id');
    }


}
