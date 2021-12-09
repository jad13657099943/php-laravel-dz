<?php


namespace Modules\Dsy\Http\Controllers\dz;


use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Dsy\Services\NodeService;

class NodeController extends Controller
{

    /**
     * 节点数据
     */
    public function node(NodeService $service){
        return $service->node();
    }

    /**
     * 节点每日数据
     * @param NodeService $service
     */
    public function dayNode(Request $request,NodeService $service){
        $type=$request->input('type',1);
        return $service->dayNode($type);
    }

}
