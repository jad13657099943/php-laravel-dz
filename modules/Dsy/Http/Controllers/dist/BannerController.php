<?php


namespace Modules\Dsy\Http\Controllers\dist;


use Illuminate\Routing\Controller;
use Modules\Dsy\Models\Banner;
use Modules\Dsy\Services\BannerService;

class BannerController extends Controller
{

    /**
     * Banner列表
     * @param BannerService $service
     * @return \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|mixed
     */
    public function banner(BannerService $service)
    {
       return $service->banner();
    }
}
