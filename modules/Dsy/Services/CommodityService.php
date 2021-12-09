<?php


namespace Modules\Dsy\Services;


use Illuminate\Support\Facades\Cache;
use Modules\Dsy\Models\Commodity;

class CommodityService
{

    /**
     * 矿机列表
     * @param int $limit
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|mixed
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function list($limit=10)
    {

        $list= Cache::get('commodity');
        if (empty($list)){
            $list = Commodity::query()->orderBy('id', 'desc')->where('state', 1)->paginate($limit);
            $data = $list->items();
            foreach ($data as $datum) {
                $datum->imgs = explode('|', $datum->imgs);
            }
            Cache::set('commodity',json_encode($list),120);
            return  $list;
        }
        return json_decode($list,true);
    }




    /**
     * 矿机详情
     * @param $id
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|mixed|object|null
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function detail($id)
    {
        $list=Cache::get('detail'.$id);
        if (empty($list)){
            $list = Commodity::query()->where('id', $id)->first();
            $list['imgs'] = explode('|', $list['imgs']);
            $data = [];
            foreach ($list['imgs'] as $img) {
                if (!empty($img)) {
                    $data[] = $img;
                }
            }
            $list['imgs'] = $data;
            Cache::set('detail'.$id,json_encode($list),120);
            return $list;
        }
        return  json_decode($list,true);

    }
}
