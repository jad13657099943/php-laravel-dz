<?php


namespace Modules\Dsy\Services;


use Modules\Dsy\Models\Order;
use Modules\Dsy\Models\UniteOrder;

class ReleaseService
{

    public function release($item,$award){
        $jc=0;//基础
        $xx=0;//线性

    }

    //fil无需质押
    public function fil($uid,$where,$T){
         $where[]=['user_id','=',$uid];
         $where[]=['fil_state','<',1];
         $list=Order::getOrder($where);
         $jc=0;//基础
         $xx=0;//线性
         foreach ($list as $item){
               $jc+=$item->save * $T * $item->bl*0.25;
               $xx+=$item->save * $T * $item->bl*0.75;
         }
         return ['jc'=>round($jc,6),'xx'=>round($xx,6)];
    }
    //fil质押
    public function fil2($uid,$where,$T){
        $where[]=['user_id','=',$uid];
        $where[]=['fil_state','>',0];
        $list=Order::getOrder($where);
        $jc=0;//基础
        $xx=0;//线性
        foreach ($list as $item){
            $jc+=$item->save * $T * $item->bl*0.25*($item->to_fil/$item->need_fil);
            $xx+=$item->save * $T * $item->bl*0.75*($item->to_fil/$item->need_fil);
        }
        return ['jc'=>round($jc,6),'xx'=>round($xx,6)];
    }

    //star chia
    public function xian($uid,$where,$T){
        $where[]=['user_id','=',$uid];
        $where[]=['fil_state','<',1];
        $list=Order::getOrder($where);
        $xx=0;//线性
        foreach ($list as $item){
            $xx+=$item->save * $T * $item->bl;
        }
        return ['xx'=>round($xx,6)];
    }

    //UNITE
    public function unite($uid,$where,$T){

        $where[]=['user_id','=',$uid];

        $list=UniteOrder::query()->where($where)->select('id','money','save','content')->get();
        $usy=0;
        $gsy=0;
        foreach ($list as $item){
               $data=$this->bl(json_decode($item->content,true)['bl'],$item->money);
               $usy+=$item->save*$data['abl']*$T;
               $gsy+=$item->save*$data['bbl']*$T;
        }
        return ['usy'=>$usy,'gsy'=>$gsy];
    }

    public function bl($bl,$money){
        $bl=json_decode($bl,true);
        foreach ($bl as $item){
            if ($money>=$item['min']&&$money<=$item['max']){
                return ['abl'=>$item['abl'],'bbl'=>$item['bbl']];
            }
        }
    }


}
