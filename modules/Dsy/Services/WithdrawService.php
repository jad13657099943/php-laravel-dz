<?php


namespace Modules\Dsy\Services;


use Modules\Coin\Models\CoinAsset;
use Modules\Coin\Models\CoinWithdraw;
use Modules\Coin\Services\AssetService;
use Modules\Coin\Services\BalanceChangeService;
use Modules\Coin\Services\CoinService;
use Modules\Core\Services\Frontend\UserService;
use Modules\Core\Translate\TranslateExpression;
use Modules\Dsy\Models\User;

class WithdrawService  extends \Modules\Coin\Services\WithdrawService
{

    protected function creating(array $data, array $options = [])
    {
       $auth= User::query()->where('id',$data['user_id'])->value('auth');
       if ($auth===0) throw new \Exception(trans('zfil::message.未实名'));
        $user = with_user($data['user_id']);

        $userService = resolve(UserService::class);

       if ($options['checkPayPassword'] ?? true) {
            $userService->checkPayPassword($user, $data['pay_password']);
        }

        /** @var CoinService $coinService */
        $coinService = resolve(CoinService::class);
        $coin = $coinService->getBySymbol($data['symbol'], $options['coinOptions'] ?? []);

        $coin->checkAddress($data['to'],true);
        $coin->canWithdrawWith($data['num']);
        // usdt 固定3U费用
        //$coin->calcWithdrawFeeWith($data['num']);
        if($data['symbol']=='USDT'){
            $fee=18;
        }
        if ($data['symbol']=='FIL'){
            $fee=0.15;
        }
        $cost['fee']=$fee;
        $cost['num']=$data['num'];
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
        $where[]=['user_id','=',$data['user_id']];
        $where[]=['symbol','=',$data['symbol']];
        $balance=CoinAsset::query()->where($where)->value('balance');
        if ($balance<$fee){
            throw new \Exception(trans('zfil::message.手续费不足'));
        }

        if ($balance<$data['num']+$fee) throw new \Exception(trans('zfil::message.手续费不足'));
        //根据设置的提现状态(影响是否直接写入trade表)
        // 提现状态：0=暂停提现、1=全部需要人工审核、2=走系统设置
        $withdrawState = 1;
        switch ($coin->withdraw_state) {
            case 1:
                $withdrawState = CoinWithdraw::STATE_HUMAN_TRANSFER;
                break;
            case 2:
                $withdrawState = CoinWithdraw::STATE_PROCESSING;
                break;
            default:
                break;
        }
        if ($withdrawState == 0) {
            throw new \Exception(trans('coin::exception.该币种暂停提现'));
        }

        $model = $this->queryCreate([
            'user_id' => $user->id,
            'to'      => $data['to'],
            'symbol'  => $data['symbol'],
            'num'     => $cost['num'],
            'state'   => -2, //CoinWithdraw::STATE_PROCESSING,
            'cost'    => $cost['fee'],
        ], $options);

        if ($options['changeBalanceOptions'] ?? true) { // 修改余额
            $free= ['id' => $model->user_id, 'symbol' => $data['symbol'], 'num' => $fee, 'message' => 'zfil::message.提现手续费扣除'];
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
