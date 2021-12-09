<?php


namespace Modules\Dsy\Models;




use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Kernel
{
    use HasApiTokens,Notifiable;

    public $table = 'users';

    public $guarded = [];

    //直推
    public static function  drive($id){
      return  self::query()->where('inviter_id',$id)->count();
    }

    //团队
    public static function team($id){
     return self::getNum($id);
    }

    //
    public  static function  getNum($id){
        $data= self::query()->where('inviter_id',$id)->pluck('id');
        $num=self::query()->where('inviter_id',$id)->count();
        foreach ($data as $datum){
            $num=$num+self::getNum($datum);
        }
        return $num;
    }

    public function user()
    {
        return $this->hasOne(ProjectUser::class, 'user_id', 'id');
    }

    public function grade(){
        return $this->hasOne(UserGrade::class,'user_id','id');
    }
}
