<?php


namespace Modules\Dsy\Http\Controllers\dist;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Dsy\Models\Information;

class InformationController extends Controller
{

    /**
     * 资讯列表
     * @param Request $request
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function list(Request $request)
    {
        $params = $request->input();
        if (empty($params['limit'])) $params['limit'] = 10;
        return Information::query()->orderBy('id', 'desc')->paginate($params['limit']);
    }



    /**
     * 资讯详情
     * @param Request $request
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|object|null
     */
    public function detail(Request $request)
    {
        $params = $request->input();
        return Information::query()->where('id', $params['id'])->first();
    }

}
