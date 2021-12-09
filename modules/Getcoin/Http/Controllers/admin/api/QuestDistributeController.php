<?php


namespace Modules\Getcoin\Http\Controllers\admin\api;


use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Getcoin\Models\QuestDistribute;
use Modules\Getcoin\Models\QuestWallet;
use Modules\Getcoin\Models\QuestWalletTrack;

class QuestDistributeController extends Controller
{

    public function index(Request $request)
    {

        $where = [];
        $param = $request->all();
        if ($param['user_id'] ?? false) {
            $where[] = ['user_id', '=', $param['user_id']];
        }
        if (isset($param['qid'])) {
            $where[] = ['qid', '=', $param['qid']];
        }

        $result = QuestDistribute::query()->where($where)
            ->with(['quest', 'user'])
            ->orderBy('id', 'desc')
            ->paginate($request->limit);

        foreach ($result as $item) {
            $item->user_name = $item->user->username;
            $item->state_text = $item->state;
            $item->title = $item->quest->title;
            $item->money_text = $item->money . $item->currency;
        }

        return $result;
    }


    /////////////  任务钱包地址管理  /////////////////////////

    /**
     * 任务钱包地址
     */
    public function wallet(Request $request)
    {

        $id = $request->input('id');
        $where[] = ['did', '=', $id];

        $result = QuestWalletTrack::query()->where($where)
            ->with('wallet')
            ->orderBy('id', 'desc')
            ->paginate($request->limit);


        return $result;
    }

    /**
     * 批量绑定生成的钱包地址
     */
    public function bindWallet(Request $request)
    {

        $id = $request->input('id');
        $minIndex = $request->input('min_index');
        $maxIndex = $request->input('max_index');
        $chain = $request->input('chain');
        if (!$minIndex || !$maxIndex || !$chain || $minIndex > $maxIndex) {
            throw new \Exception('信息填写错误');
        }

        $distribute = QuestDistribute::query()->where('id', $id)->first();

        //查询出之前生成的钱包地址
        $walletList = QuestWallet::query()->where('chain', $chain)
            ->whereBetween('index', [$minIndex, $maxIndex])
            ->where('user_id', $distribute->user_id)
            ->orderBy('id', 'asc')
            ->get();
        if ($walletList->isEmpty()) {
            throw new \Exception('未找到相应的可绑定的钱包地址');
        }

        //查询已绑定的钱包，不要重复绑定
        $alreadyList = QuestWalletTrack::query()->where('user_id', $distribute->user_id)
            ->where('chain', $chain)
            ->where('did', $id)
            ->pluck('wallet_id')->toArray();

        //把搜索到的钱包地址添加到任务关联表
        $arr = [];
        foreach ($walletList as $item) {

            if (!in_array($item->id, $alreadyList)) {
                $arr[] = [
                    'wallet_id' => $item->id,
                    'did' => $distribute->id,
                    'user_id' => $distribute->user_id,
                    'qid' => $distribute->qid,
                    'chain' => $chain,
                    'index' => $item->index,
                    'address' => $item->address,
                    'created_at' => date('Y-m-d H:i:s')
                ];
            }
        }

        if (empty($arr)) {
            throw new \Exception('未找出可添加的任务钱包');
        }

        \DB::table('quest_wallet_track')->insert($arr);
        return ['msg' => '操作成功'];
    }

    /**
     * 删除已绑定的钱包记录
     */
    public function delWallet(Request $request)
    {

        $id = $request->input('id');
        QuestWalletTrack::query()->where('id', $id)->delete();
        return ['msg' => '删除成功'];
    }

}
