<?php


namespace Modules\Zfil\Services\Traits;


use Modules\Coin\Services\AssetService;
use Modules\Coin\Services\CoinService;

trait CoinTraits
{
    /**
     * 获取单币种余额
     * @param $symbol
     * @param $userId
     * @return int[]
     */
    public function getCoinBalance($symbol, $userId)
    {

        $service = resolve(AssetService::class);
        $info = $service->one([
            'user_id' => $userId,
            'symbol' => $symbol
        ], [
            'exception' => false,
        ]);

        $value = $info->balance ?? 0;
        /*if ($value > 0) {

            //折算成cny价值
            $coinService = resolve(CoinService::class);
            $usdtCnyPrice = $coinService->getUsdtPrice();
            $balanceCny = bcmul($value, $usdtCnyPrice['CNY'], 2);
        }*/

        $data=[
            'balance' => floatval($value)
        ];
        return success('币种余额',$data);

    }
}
