<?php


namespace Modules\Dsy\Http\Controllers\dist;


use App\Http\Controllers\Controller;
use App\Jobs\RecordJob;
use Illuminate\Http\Request;
use Modules\Core\Services\Frontend\UserService;
use Modules\Dsy\Models\CoinAsset;
use Modules\Dsy\Models\Commodity;
use Modules\Dsy\Models\Message;
use Modules\Dsy\Models\Order;
use Modules\Dsy\Models\ProjectUser;
use Modules\Dsy\Models\Record;
use Modules\Dsy\Models\Team;
use Modules\Dsy\Models\Teams;
use Modules\Dsy\Models\User;
use Modules\Dsy\Models\UserGrade;
use Modules\Dsy\Services\OrderService;
use Modules\Dsy\Services\PublicService;
use function Composer\Autoload\includeFile;

class OrderController extends Controller
{

    /**
     * 立即下单
     * @param Request $request
     * @return array
     */
    public function placeAnOrder(Request $request)
    {
        $user = $request->user();
        $params = $request->input();
        $list = Commodity::query()->where('id', $params['id'])->select('id', 'title', 'money', 'save', 'period', 'start_time')->first();
        $data = [
            'name' => $user->username,
            'list' => $list
        ];
        return $data;
    }





    /**
     * 立即支付
     * @param Request $request
     * @param PublicService $service
     * @param OrderService $orderService
     * @return mixed
     * @throws \Exception
     */
    public function payOrder(Request $request, PublicService $service,OrderService $orderService)
    {
        $uid = $request->user()['id'];

        $params = $request->input();

        $num = empty($params['num']) ? 1 : $params['num'];

        $service->check_password($uid, $params['pay_password']);

        $list = $orderService->verification($params['id'], $uid, $num);

      //  $service->check_restrict($uid,$list['chain']);

        $money = $list['money'] * $num;

        $save = $list['saves'] * $num;

        return \DB::transaction(function () use ($uid, $params, $list, $money, $num, $save,$orderService) {

            $buy = ['id' => $uid, 'symbol' => $list['symbol'], 'num' => $money, 'mid' => 0];
            buy($buy);

            $orderId=$orderService->createOrder($list,$save,$uid,$num,$money,date('Y-m-d H:i:s'));

            Commodity::subtract($params['id']);
            //假设有性能压力队列处理
         /*   $job=[
                'chain'=>$list['chain'],
                'uid'=>$uid,
                'money'=>$money,
                'symbol'=>$list['symbol'],
                'order_id'=>$orderId
            ];

            RecordJob::dispatch($job)->onQueue('high');*/

            $teams = Teams::getTeams($list['chain']);

            $orderService->up($uid, $list['chain'], $teams,$money,$list['symbol'],$orderId);

            $grade = UserGrade::getGrade($uid, $list['chain']);

            $orderService->upDai($uid, $grade, $list['chain'], $teams);
        });
    }







}
