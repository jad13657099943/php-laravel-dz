<?php

namespace Modules\Coinv2\Services;

use DB;
use Modules\Coin\Models\CoinRecharge;
use Modules\Core\Exceptions\ModelSaveException;
use Modules\Core\Services\Traits\HasQuery;
use Modules\Core\Translate\TranslateExpression;

class RechargeService
{
    use HasQuery {
        create as queryCreate;
    }

    /**
     * @var CoinRecharge
     */
    protected $model;

    /**
     * RechargeService constructor.
     *
     * @param TradeService $tradeService
     */
    public function __construct(CoinRecharge $model)
    {
        $this->model = $model;
    }

    /**
     * @param $hash
     * @param array $options
     *
     * @return CoinRecharge|mixed
     */
    public function getByHash($hash, array $options = [])
    {
        return $this->one(['hash' => $hash], $options);
    }

    /**
     * @param array $data
     * @param array $options
     *
     * @return bool|CoinRecharge
     * @throws ModelSaveException
     * @throws \Modules\Coinv2\Exceptions\CoinNotFoundException
     * @throws \Throwable
     */
    public function createWithToWallet(array $data, array $options = [])
    {
        /** @var UserWalletService $walletService */
        $walletService = resolve(UserWalletService::class);
        $wallet = $walletService->getByAddress($data['to']);

        $recharge = $this->create([
            'user_id'      => $wallet->user_id,
            'symbol'       => $data['symbol'],
            'chain'        => $data['chain'],
            'from'         => $data['from'],
            'to'           => $data['to'],
            'value'        => $data['value'],
            'memo'         => $data['memo'],
            'hash'         => $data['hash'],
            'state'        => $data['state'],
            'block_number' => $data['block_number'],
            'packaged_at'  => $data['packaged_at'],
        ], $options);
    }

    /**
     * @param array $data
     * @param array $options
     *
     * @return bool|\Illuminate\Database\Eloquent\Model
     * @throws ModelSaveException
     * @throws \Modules\Coinv2\Exceptions\CoinNotFoundException
     * @throws \Throwable
     */
    public function create(array $data, array $options = [])
    {
        if ($options['transaction'] ?? false) {
            return DB::transaction(function () use ($data, $options) {
                // ?????????????????????
                $options['transaction'] = false;
                $options['changeBalanceOptions'] = array_merge(
                    $options['changeBalanceOptions'] ?? [],
                    ['transaction' => false]
                );

                return $this->creating($data, $options);
            });
        }

        return $this->creating($data, $options);
    }

    protected function creating($data, $options)
    {
        /** @var CoinService $coinService */
        $coinService = resolve(CoinService::class);
        $coin = $coinService->getBySymbol($data['symbol']);

        $coin->canRechargeWith($data['value']);

        $model = $this->queryCreate($data, $options);

        if ($options['changeBalanceOptions'] ?? true) { // ????????????
            $balanceChangeService = resolve(BalanceChangeService::class);
            $changeBalanceOptions = $options['changeBalanceOptions'] ?? [];
            $balanceChangeService
                ->to($model->user_id)
                ->withSymbol($model->symbol)
                ->withNum($model->value)
                ->withNo($model->id)
                ->withModule('coin.recharge_from')
                ->withInfo($changeBalanceOptions['info'] ?? new TranslateExpression('coin::message.??????'))
                ->change($changeBalanceOptions);
        }

        return $model;
    }
}
