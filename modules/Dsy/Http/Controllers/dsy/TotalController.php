<?php


namespace Modules\Dsy\Http\Controllers\dsy;


use App\Http\Controllers\Controller;
use Modules\Dsy\Services\TotalService;


class TotalController extends Controller
{

    /**
     * 统计基础收益
     */
    public function total(){
        $service = resolve(TotalService::class);
        $service->total();
    }


    /**
     * 统计团队收益
     */
    public function totals(){
        $service = resolve(TotalService::class);
        $service->totals();
    }

    /**
     * 矫正余额
     */
    public function del(){
        $service = resolve(TotalService::class);
        $service->del();
    }


}
