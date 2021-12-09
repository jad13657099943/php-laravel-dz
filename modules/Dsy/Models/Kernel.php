<?php


namespace Modules\Dsy\Models;


use Illuminate\Database\Eloquent\Model;

class Kernel extends Model
{

    public static function where($where){
        return self::query()->where($where);
    }

    public static function whereFirst($where){
        return self::query()->where($where)->first();
    }

    public static function whereValue($where,$value){
        return self::query()->where($where)->value($value);
    }

    public static function whereDelete($where){
        return self::query()->where($where)->delete();
    }

    public static function whereUpdate($where,$data){
        return self::query()->where($where)->update($data);
    }

    public static function whereInsert($data){
        return self::query()->insert($data);
    }

    public static function kGet(){
        return self::query()->orderBy('id','desc')->get();
    }

    public static function whereGet($where){
        return self::query()->where($where)->orderBy('id','desc')->get();
   }

   public static function KPaginate($limit=10){
        return self::query()->orderBy('id','desc')->paginate($limit);
   }

   public static function wherePaginate($where,$limit=10){
       return self::query()->orderBy('id','desc')->where($where)->paginate($limit);
   }

   public static function wherePluck($where,$pluck){
        return self::query()->where($where)->pluck($pluck);
   }

   public static function whereSum($where,$sum){
        return self::query()->where($where)->sum($sum);
   }

   public static function whereCount($where){
        return self::query()->where($where)->count();
   }

   public static function whereDecrement($where,$decrement,$num){
        return self::query()->where($where)->decrement($decrement,$num);
   }

    public static function whereIncrement($where,$increment,$num){
        return self::query()->where($where)->increment($increment,$num);
    }

 }
