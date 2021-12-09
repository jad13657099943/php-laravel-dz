<?php


namespace Modules\Dsy\Models;



use Illuminate\Database\Eloquent\Model;

class CoinAsset extends Model
{
    public $table = 'coin_asset';
    public $guarded = [];

    public static function getUsdt($id){
        return self::query()->where('user_id',$id)->where('symbol','USDT')->value('balance');
    }

    public static function getFil($id){
        return self::query()->where('user_id',$id)->where('symbol','FIL')->value('balance');
    }

    public static function getBalance($uid,$symbol){
        return self::query()->where('user_id',$uid)->where('symbol',$symbol)->value('balance');
    }

}
