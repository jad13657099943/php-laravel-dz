<?php


namespace Modules\Dsy\Http\Controllers\dsy;


use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Modules\Dsy\Models\CoinAsset;
use Modules\Dsy\Models\CoinLog;
use Modules\Dsy\Models\Commodity;
use Modules\Dsy\Models\Dsy\Chain;
use Modules\Dsy\Models\Dsy\Name;
use Modules\Dsy\Models\Dsy\Not;
use Modules\Dsy\Models\Dsy\Price;
use Modules\Dsy\Models\Dsy\Total;
use Modules\Dsy\Models\Order;
use Modules\Dsy\Models\Record;
use Modules\Dsy\Models\Team;
use Modules\Dsy\Models\Unite;
use Modules\Dsy\Models\UniteOrder;
use Modules\Dsy\Models\User;
use Modules\Dsy\Models\UserGrade;
use Modules\Dsy\Models\UserSy;
use Modules\Dsy\Services\IndexService;

class IndexController extends Controller
{

    /**
     * 栏目
     * @param Request $request
     * @param IndexService $service
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function chainList(Request $request,IndexService $service)
    {
        $limit = $request->input()['limit']??10;
        return $service->chainList($limit);
    }


    /**
     * 联合挖矿
     * @param Request $request
     * @param IndexService $service
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function uniteList(Request $request,IndexService $service)
    {
        $limit = $request->input()['limit']??10;
        return $service->uniteList($limit);
    }


    /**
     * 存储挖矿
     * @param Request $request
     * @param IndexService $service
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function commodityList(Request $request,IndexService $service)
    {
        $params=$request->input();
        return $service->commodityList($params);
    }


    /**
     * 我的订单
     * @param Request $request
     * @param IndexService $service
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function orderList(Request $request,IndexService $service)
    {
        $uid = $request->user()['id'];
        $params = $request->input();
        return $service->orderList($params,$uid);
    }


    /**
     * 我的资产
     * @param Request $request
     * @param IndexService $service
     * @return array
     */
    public function balanceList(Request $request,IndexService $service)
    {
        $uid = $request->user()['id'];
        $params = $request->input();
        return $service->balanceList($params,$uid);
    }

    /**
     * 我的收益
     * @param Request $request
     * @param IndexService $service
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function syList(Request $request,IndexService $service)
    {
        $uid = $request->user()['id'];
        $params = $request->input();
        return $service->syList($params,$uid);
    }


    /**
     * 我的收入支出
     * @param Request $request
     * @param IndexService $service
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function incomeExpensesList(Request $request,IndexService $service)
    {
        $uid = $request->user()['id'];
        $params = $request->input();
        return $service->incomeExpensesList($params,$uid);
    }


    /**
     * 我的信息
     * @param Request $request
     * @param IndexService $service
     * @return array
     */
    public function userMessage(Request $request,IndexService $service)
    {
        $uid = $request->user()['id'];
        return $service->userMessage($uid);
    }

    /**
     * 我的等级
     * @param Request $request
     * @param IndexService $service
     * @return \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public function userGrade(Request $request,IndexService $service)
    {
        $uid = $request->user()['id'];
        return $service->userGrade($uid);
    }

    /**
     * 邀请信息
     * @param Request $request
     * @param IndexService $service
     * @return array
     */
    public function invite(Request $request,IndexService $service)
    {
        $uid = $request->user()['id'];
        return $service->invite($uid);
    }

    /**
     * 币种列表
     * @param Request $request
     * @param IndexService $service
     * @return \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public function symbolList(Request $request,IndexService $service)
    {
        $uid = $request->user()['id'];
       return $service->symbolList($uid);
    }

    /**
     * 手续费
     * @param Request $request
     * @param IndexService $service
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|object|null
     */
    public function freeList(Request $request,IndexService $service){
        $params = $request->input();
        return $service->freeList($params);
    }

}
