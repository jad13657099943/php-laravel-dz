<?php


namespace Modules\Getcoin\Http\Controllers\admin;


use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Coin\Models\Coin;
use Modules\Getcoin\Models\QuestDistribute;
use Modules\Getcoin\Models\QuestList;
use Modules\Getcoin\Models\QuestWallet;

class QuestDistributeController extends Controller
{

    public function index()
    {
        $questList = QuestList::query()->select('id', 'title')->get();
        return view('getcoin::admin.quest_distribute.index', [
            'quest' => $questList
        ]);
    }

    /*public function create()
    {
        return view('getcoin::admin.quest_distribute.create');
    }

    public function editInfo(Request $request)
    {

        $id = $request->input('id');
        $info = QuestDistribute::query()->find($id);
        return view('getcoin::admin.quest_distribute.edit', [
            'info' => $info
        ]);
    }*/


    public function wallet(Request $request)
    {
        $id = $request->input('id');
        $userId = QuestDistribute::query()->where('id', $id)->value('user_id');
        $coin = QuestWallet::query()->where('user_id', $userId)
            ->groupBy('chain')
            ->orderBy('index', 'desc')
            ->get();

        $msg = '';
        foreach ($coin as $item) {
            $msg .= $item->chain . '最大编号：' . $item->index . '、';
        }

        return view('getcoin::admin.quest_distribute.wallet', [
            'id' => $id,
            'msg' => $msg,
        ]);
    }


    public function bindWallet(Request $request)
    {

        $id = $request->input('id');
        $coin = Coin::query()->where('tokenio_version', 2)
            ->groupBy('chain')
            ->pluck('chain');

        return view('getcoin::admin.quest_distribute.bind_wallet', [
            'id' => $id,
            'coin' => $coin
        ]);
    }

}
