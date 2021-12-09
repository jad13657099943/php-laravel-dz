<?php


namespace Modules\Dsy\Services;


use Illuminate\Support\Facades\Cache;
use Modules\Dsy\Models\CoinAsset;
use Modules\Dsy\Models\CoinLog;

class TreeService
{
    /**
     * 计算收益
     * @param $uid
     * @param $symbol
     * @param $action
     * @return int|mixed
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function earnings($uid,$symbol,$action){
         $num=Cache::get($uid.$symbol.$action);
         if (empty($num)){
             $num=CoinLog::getNum($uid,$symbol,$action);
             Cache::set($uid.$symbol.$action,$num,300);
         }
         return $num;
    }
}
