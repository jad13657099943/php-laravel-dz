<?php


namespace Modules\Dsy\Services;


use phpDocumentor\Reflection\DocBlock\Tags\Var_;

class NodeService
{
    /**
     * 节点数据
     * @return array
     */
    public function node(){
        $data=[
            'all_node'=>12345678765,
            'day_node'=>1234,
            'all_bill'=>4567890987654678,
            'all_balance'=>12345671234,
            'day_balance'=>1233456787654,
            'symbol'=>'BZZ'
        ];
        return $data;
    }


    /**
     * 节点详情
     * @param int $type
     */
    public function dayNode($type=1){

          $data=[];
          $time=date('Y-m-d');
          for ($i=0;$i<7;$i++){
              $data['time'][$i]=[
                'time'=>date('Y-m-d',strtotime($time)+86400*$i),
                'num'=>$i,
                'value'=>$i
              ];
        }
          $data['nodes']= [820, 932, 901, 934, 1290, 1330, 1320];
          $data['bill']=[1320, 1220, 1180, 1100, 1080, 820, 600];
          $data['amount']=[1320, 1100, 900, 1000, 1100, 1200, 1100];
           switch ($type){
               case 1:
                   return $data;
                   break;
               case 2:
                   return  $data;
                   break;
               case 3:
                   return  $data;
                   break;
           }
          return  $data;
    }
}
