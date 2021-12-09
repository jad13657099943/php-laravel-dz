<?php

namespace Modules\Coin\Http\Controllers\Api;

use Illuminate\Routing\Controller;
use Modules\Coin\Http\Requests\AssetListRequest;
use Modules\Coin\Http\Requests\AssetsLogListRequest;
use Modules\Coin\Services\AssetService;
use Modules\Coin\Services\CoinLogModuleService;
use Modules\Coin\Services\CoinLogService;
use Modules\Coin\Models\CoinLog;

class AssetController extends Controller
{

    /**
     * 获取用户资产列表
     *
     * coin和price为空 代表没匹配到币设置. 前端需要过滤处理
     */
    public function index(AssetListRequest $request, AssetService $assetService)
    {
        return $assetService->getAllByUser($request->user(), [
            'with' => ['coin'],
            'assetIsExist' => true
        ])->map(function ($data) {
            if ($data->coin) {
                $data->coin->append(['price']);
            }

            return $data->append(['balance_price']);
        });
    }

    /**
     * 获取资产变更明细
     */
    public function logList(AssetsLogListRequest $request, CoinLogService $coinlogService)
    {
        $input = $request->validationData();
        $options = [
            'paginate' => true,
            'page' => $input['page'],
            'limit' => $input['limit'] ?? null,
            'orderBy' => $input['order'] ?? ['created_at', 'DESC'],
        ];
        $where = [];
        if (isset($input['module']) && $input['module']) $where[] = ['module', '=', $input['module']];
        if (isset($input['action']) && $input['action']) $where[] = ['action', '=', $input['action']];
        if (isset($input['type']) && $input['type'] != 'all') {
            if ($input['type'] == 'inc') {
                $where[] = ['num', '>', 0];
            } else {
                $where[] = ['num', '<', 0];
            }
        }


        $data = $coinlogService->getAllBySymbol($request->user(), $input['symbol'], $where, $options);

        return $data;
    }

    /**
     * 获取资产变更模块标识
     */
    public function logModuleList(CoinLogModuleService $coinLogModuleService)
    {

        return $coinLogModuleService->all([],
            ['appendActions' => true, 'getArray' => true]
        )->map(function ($module) {
            $module['name'] = trans('log_module.' . $module['name']);
            return $module;
        });
    }
}
