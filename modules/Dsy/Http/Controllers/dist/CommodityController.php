<?php


namespace Modules\Dsy\Http\Controllers\dist;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Dsy\Models\Commodity;
use Modules\Dsy\Services\CommodityService;

class CommodityController extends Controller
{

    /**
     * 矿机列表
     * @param Request $request
     * @param CommodityService $service
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|mixed
     */
    public function list(Request $request,CommodityService $service)
    {
        $limit=$request->limit??10;
        return $service->list($limit);

    }





    /**
     * 矿机详情
     * @param Request $request
     * @param CommodityService $service
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|mixed|object|null
     */
    public function detail(Request $request,CommodityService $service)
    {
        $params = $request->input();
        return $service->detail($params['id']);
    }
}
