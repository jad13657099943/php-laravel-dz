<?php


namespace Modules\Dsy\Models;


use Illuminate\Database\Eloquent\Model;

class Order extends Kernel
{
    public $table = 'order';
    public $guarded = [];

    public static $state = [
        1 => '已支付',
        2 => '进行中',
        3 => '已完成'
    ];

    public static $type = [
        '1' => 'app购买',
        '2' => 'USDT',
        '3' => '公账',
        '4' => '私账'
    ];

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    //获取订单
    public static function getOrder($where)
    {
        return self::query()->where($where)->select('id', 'user_id', 'to_symbol', 'chain', 'to_type', 'save', 'bl', 'need_fil', 'to_fil')->get();
    }

    //质押量
    public static function getFil($uid)
    {
        return self::query()->where('user_id', $uid)->sum('to_fil');
    }
}
