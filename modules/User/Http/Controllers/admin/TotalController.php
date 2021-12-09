<?php


namespace Modules\User\Http\Controllers\admin;


use App\Models\User;
use Illuminate\Routing\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Modules\Coin\Models\Coin;
use Modules\Coin\Models\CoinAsset;
use Modules\Coin\Models\CoinLog;
use Modules\Coin\Models\CoinRecharge;
use Modules\Coin\Models\CoinWithdraw;

class TotalController extends Controller
{

    /**
     * 驾驶舱统计数据
     */
    public function total()
    {

        $dayStart = Carbon::now()->startOfDay();
        $dayEnd = Carbon::now()->endOfDay();
        $weekStart = Carbon::now()->startOfWeek();
        $weekEnd = Carbon::now()->endOfWeek();
        $monthStart = Carbon::now()->startOfMonth();
        $monthEnd = Carbon::now()->endOfMonth();
        $yearStart = Carbon::now()->startOfYear();
        $yearEnd = Carbon::now()->endOfYear();

        //币种资产充提统计
        $coin = Coin::query()->where('status', 1)->pluck('symbol');
        $coinTotal = [];
        foreach ($coin as $item) {

            //剩余总数
            $balance = CoinAsset::query()->where('symbol', $item)
                ->sum('balance');
            //充值
            $recharge['total'] = CoinRecharge::query()->where('symbol', $item)
                ->select(\DB::raw('sum(value) as total'),\DB::raw('count(*) as num'))
                ->first()->toArray();
            $recharge['day'] = CoinRecharge::query()->where('symbol', $item)
                ->whereBetween('created_at', [$dayStart, $dayEnd])
                ->select(\DB::raw('sum(value) as total'),\DB::raw('count(*) as num'))
                ->first()->toArray();
            $recharge['week'] = CoinRecharge::query()->where('symbol', $item)
                ->whereBetween('created_at', [$weekStart, $weekEnd])
                ->select(\DB::raw('sum(value) as total'),\DB::raw('count(*) as num'))
                ->first()->toArray();
            $recharge['month'] = CoinRecharge::query()->where('symbol', $item)
                ->whereBetween('created_at', [$monthStart, $monthEnd])
                ->select(\DB::raw('sum(value) as total'),\DB::raw('count(*) as num'))
                ->first()->toArray();
            $recharge['year'] = CoinRecharge::query()->where('symbol', $item)
                ->whereBetween('created_at', [$yearStart, $yearEnd])
                ->select(\DB::raw('sum(value) as total'),\DB::raw('count(*) as num'))
                ->first()->toArray();
            //提现
            $withdraw['total'] = CoinWithdraw::query()->where('symbol', $item)
                ->where('state', 2)
                ->select(\DB::raw('sum(num) as total'),\DB::raw('count(*) as num'))
                ->first()->toArray();
            $withdraw['day'] = CoinWithdraw::query()->where('symbol', $item)
                ->where('state', 2)
                ->whereBetween('created_at', [$dayStart, $dayEnd])
                ->select(\DB::raw('sum(num) as total'),\DB::raw('count(*) as num'))
                ->first()->toArray();
            $withdraw['week'] = CoinWithdraw::query()->where('symbol', $item)
                ->where('state', 2)
                ->whereBetween('created_at', [$weekStart, $weekEnd])
                ->select(\DB::raw('sum(num) as total'),\DB::raw('count(*) as num'))
                ->first()->toArray();
            $withdraw['month'] = CoinWithdraw::query()->where('symbol', $item)
                ->where('state', 2)
                ->whereBetween('created_at', [$monthStart, $monthEnd])
                ->select(\DB::raw('sum(num) as total'),\DB::raw('count(*) as num'))
                ->first()->toArray();
            $withdraw['year'] = CoinWithdraw::query()->where('symbol', $item)
                ->where('state', 2)
                ->whereBetween('created_at', [$yearStart, $yearEnd])
                ->select(\DB::raw('sum(num) as total'),\DB::raw('count(*) as num'))
                ->first()->toArray();
            //后台拨币总金额
            $adminAddNum = CoinLog::query()->where('action', 'admin_change')
                ->where('symbol', $item)
                ->where('num', '>', 0)
                ->sum('num');
            $adminDecNum = CoinLog::query()->where('action', 'admin_change')
                ->where('symbol', $item)
                ->where('num', '<', 0)
                ->sum('num');
            $coinTotal[$item] = [
                'balance' => floatval($balance),
                'admin_add_num' => floatval($adminAddNum),
                'admin_dec_num' => floatval($adminDecNum),
                'recharge' => $recharge,
                'withdraw' => $withdraw,
            ];
        }


        ////////////////// 具体业务 ////////////////////////

        //注册人数
        $user['total'] = User::query()->count();
        $user['day'] = User::query()->whereBetween('created_at', [$dayStart, $dayEnd])->count();
        $user['week'] = User::query()->whereBetween('created_at', [$weekStart, $weekEnd])->count();
        $user['month'] = User::query()->whereBetween('created_at', [$monthStart, $monthEnd])->count();
        $user['year'] = User::query()->whereBetween('created_at', [$yearStart, $yearEnd])->count();

        //会员等级人数统计（具体业务具体分析）
        /*$gradeSet = MfcUser::$gradeMap;
        $grade = MfcUser::query()->groupBy('grade')->select('grade', \DB::raw('count(*) as num'))->pluck('num', 'grade')->toArray();
        $gradeTotal = [];
        for ($i = 0; $i < count($gradeSet); $i++) {
            $gradeTotal[$i] = [
                'name' => $gradeSet[$i],
                'num' => $grade[$i] ?? 0
            ];
        }*/

        return view('user::admin.total.total', [
            'user' => $user,
            'grade' => $gradeTotal ?? [],
            'coin' => $coinTotal,
        ]);
    }


    /**
     * 手动调整资产
     */
    public function asset()
    {

        $coin = Coin::query()->where('status', 1)->pluck('symbol');
        return view('user::admin.total.asset', [
            'coin' => $coin,
        ]);
    }

}
