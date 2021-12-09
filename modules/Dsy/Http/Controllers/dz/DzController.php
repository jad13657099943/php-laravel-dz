<?php


namespace Modules\Dsy\Http\Controllers\dz;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Coin\Models\CoinTokenioNotice;
use Modules\Coinv2\Services\TokenioNoticeService;
use Modules\Dsy\Services\DzService;
use Modules\Dsy\Services\PublicService;

class DzController extends Controller
{


    /**
     * 我的团队信息
     * @param Request $request
     * @param DzService $service
     * @return array
     */
    public function team(Request $request,DzService $service){
             $uid=$request->user()['id'];
             $grade= $service->grade($uid);
             $number=$service->number($uid);
             $performance=$service->performance($uid);
             return [
               'grade'=>$grade,
               'number'=>$number,
               'performance'=>$performance
             ];
    }

    /**
     * 我的团队列表
     * @param Request $request
     * @param DzService $service
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function teamList(Request $request,DzService $service){
        $uid=$request->user()['id'];
      return  $service->directList($uid,$request->limit??10);
    }

    /**
     * 充值
     * @throws \Throwable
     */
    public function recharge()
    {
        $service = resolve(TokenioNoticeService::class);
        $list = $service->all([
            'state' => 0,
            'version' => 2,
        ], [
            'limit' => 10,
            'orderBy' => ['id', 'asc']
        ]);

        if ($list->isNotEmpty()) {

            foreach ($list as $item) {
                $service->process($item);
            }
        }
    }

}
