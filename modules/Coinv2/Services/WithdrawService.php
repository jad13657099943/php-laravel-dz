<?php

namespace Modules\Coinv2\Services;

use Illuminate\Support\Facades\DB;
use Modules\Coin\Models\CoinAsset;
use Modules\Coin\Models\CoinWithdraw;
use Modules\Core\Services\Frontend\UserService;
use Modules\Core\Services\Traits\HasQuery;
use Modules\Core\Translate\TranslateExpression;
use Modules\Dsy\Models\Dsy\Price;

class WithdrawService
{
    use HasQuery {
        create as queryCreate;
    }

    /**
     * @var CoinWithdraw
     */
    protected $model;

    /**
     * RechargeService constructor.
     *
     * @param TradeService $tradeService
     */
    public function __construct(CoinWithdraw $model)
    {
        $this->model = $model;
    }

    /**
     * @param array $data
     * @param array $options
     *
     * @return bool|\Illuminate\Database\Eloquent\Model|mixed
     * @throws \Modules\Coinv2\Exceptions\CoinException
     * @throws \Throwable
     */
    public function create(array $data, array $options = [])
    {
        if ($options['transaction'] ?? true) {
            return DB::transaction(function () use ($data, $options) {
                // 内部不需要事务
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

    /**
     * @param array $data
     * @param array $options
     *
     * @return bool|\Illuminate\Database\Eloquent\Model
     * @throws \Modules\Coinv2\Exceptions\CoinException
     */
    protected function creating(array $data, array $options = [])
    {
        $user = with_user($data['user_id']);

        $userService = resolve(UserService::class);

        if ($options['checkPayPassword'] ?? true) {
            $userService->checkPayPassword($user, $data['pay_password']);
        }

        /** @var CoinService $coinService */
        $coinService = resolve(CoinService::class);
        // $coin = $coinService->getBySymbol($data['symbol'], $options['coinOptions'] ?? []);

        //验证钱包地址正确性
        if ($data['chain'] == 'TRX') {
            $coinService->checkAddress($data['chain'], $data['to'], true);
            $T = substr($data['to'], 0, 1);
            if ($T != 'T') throw new \Exception(trans('错误的地址:' . $data['to']));
        }

        //$coin->canWithdrawWith($data['num']);
        // $cost = $coin->calcWithdrawFeeWith($data['num']);
        $list = Price::query()->where('symbol', $data['symbol'])->select('free', 'min', 'max', 'withdraw')->first();
        $cost['fee'] = $list['free'];
        $cost['num'] = $data['num'];
        if ($data['num'] < $list['min']) {
            throw new \Exception('最小提现数量' . $list['min']);
        }
        if ($data['num'] > $list['max']) {
            throw new \Exception('最大提现数量' . $list['max']);
        }
        //判断该用户币种资产释放单独冻结
        $coinAssetService = resolve(AssetService::class);
        $coinAsset = $coinAssetService->one([
            'user_id' => $data['user_id'],
            'symbol' => $data['symbol'],
            'status' => 1,
            'frozen' => 0,
        ], [
            'exception' => false
        ]);
        if (empty($coinAsset)) {
            throw new \Exception($data['symbol'] . trans('coin::exception.已冻结不能提现'));
        }

        $where[] = ['user_id', '=', $data['user_id']];
        $where[] = ['symbol', '=', $list['withdraw']];
        $balance = CoinAsset::query()->where($where)->value('balance');

        if ($balance < $list['free']) {
            throw new \Exception(trans('dsy::message.手续费不足'));
        }

        if ($data['symbol'] == $list['withdraw']) {
            if ($balance < $data['num'] + $list['free']) throw new \Exception(trans('dsy::message.手续费不足'));
        }
        //根据设置的提现状态(影响是否直接写入trade表)
        // 提现状态：0=暂停提现、1=全部需要人工审核、2=走系统设置
        /* $withdrawState = 0;
         switch ($coin->withdraw_state) {
             case 1:
                 $withdrawState = CoinWithdraw::STATE_HUMAN_TRANSFER;
                 break;
             case 2:
                 $withdrawState = CoinWithdraw::STATE_PROCESSING;
                 break;
             default:
                 break;
         }*/
        /*if ($withdrawState == 0) {
            throw new \Exception(trans('coin::exception.该币种暂停提现'));
        }*/

        $model = $this->queryCreate([
            'user_id' => $user->id,
            'to' => $data['to'],
            'symbol' => $data['symbol'],
            'num' => $cost['num'],
            'state' => -2, //CoinWithdraw::STATE_PROCESSING,
            'cost' => $cost['fee'],
            'chain' => $data['chain']
        ], $options);

        if ($options['changeBalanceOptions'] ?? true) { // 修改余额
            $free = ['id' => $model->user_id, 'symbol' => $list['withdraw'], 'num' => $list['free'], 'message' => 'dsy::message.提现手续费扣除'];
            free($free);
            $balanceChangeService = resolve(BalanceChangeService::class);
            $changeBalanceOptions = $options['changeBalanceOptions'] ?? [];
            $balanceChangeService
                ->from($model->user_id)
                ->withSymbol($model->symbol)
                ->withNum($data['num'])
                ->withModule('coin.withdraw_to')
                ->withNo($model->id)
                ->withInfo(
                    $changeBalanceOptions['info'] ??
                    new TranslateExpression('coin::message.提现扣除', ['num' => $model->num, 'cost' => $model->cost])
                )
                ->change($changeBalanceOptions);
        }

        return $model;
    }
}
