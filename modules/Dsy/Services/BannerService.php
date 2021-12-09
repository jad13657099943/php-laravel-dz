<?php


namespace Modules\Dsy\Services;


use Illuminate\Support\Facades\Cache;
use Modules\Dsy\Models\Banner;

class BannerService
{


    public function banner()
    {
        $list= Cache::get('banner');
        if (empty($list)){
            $list= Banner::query()->get();
            Cache::set('banner',json_encode($list),120);
            return $list;
        }
        return json_decode($list,true);
    }
}
