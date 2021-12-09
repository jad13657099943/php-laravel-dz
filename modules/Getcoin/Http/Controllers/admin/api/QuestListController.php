<?php


namespace Modules\Getcoin\Http\Controllers\admin\api;


use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Getcoin\Models\QuestList;

class QuestListController extends Controller
{

    public function index(Request $request)
    {

        $where = [];
        $param = $request->all();
        if ($param['symbol'] ?? false) {
            $where[] = ['symbol', '=', $param['symbol']];
        }
        if (isset($param['state']) && is_numeric($param['state'])) {
            $where[] = ['state', '=', $param['state']];
        }
        $result = QuestList::query()->where($where)
            ->orderBy('id', 'desc')
            ->paginate($request->limit);

        foreach ($result as $item) {
            $item->type_text = $item->type;
            $item->state_text = $item->state;
            $item->is_show_text = $item->is_show;
        }

        return $result;
    }


    /**
     * 添加产品
     * @param GoodsRequest $request
     * @param GoodsService $goodsService
     * @return string[]
     * @throws \Modules\Core\Exceptions\ModelSaveException
     */
    public function create(Request $request)
    {

        $param = [
            'enroll_num' => $request->input('enroll_num'),
            'release_num' => 0,
            'title' => $request->input('title'),
            'money_cny' => $request->input('money_cny',0),
            'money_usd' => $request->input('money_usd',0),
            'unit' => $request->input('unit'),
            'state' => $request->input('state',0),
            'is_show' => $request->input('is_show',0),
            'sort' => $request->input('sort',0),
            'content' => $request->input('content'),
            'image' => $request->input('image'),
            'summary' => $request->input('summary'),
            'cost_content' => $request->input('cost_content'),
            'reward_content' => $request->input('reward_content'),
        ];
        $model = new QuestList($param);
        $model->save();
        return response()->redirectTo(route('m.getcoin.admin.quest_list.index'));
    }

    public function editInfo(Request $request)
    {

        $id = $request->input('id');
        $param = [
            'enroll_num' => $request->input('enroll_num'),
            'release_num' => 0,
            'title' => $request->input('title'),
            'money_cny' => $request->input('money_cny',0),
            'money_usd' => $request->input('money_usd',0),
            'unit' => $request->input('unit'),
            'state' => $request->input('state',0),
            'is_show' => $request->input('is_show',0),
            'sort' => $request->input('sort',0),
            'content' => $request->input('content'),
            'image' => $request->input('image'),
            'summary' => $request->input('summary'),
            'cost_content' => $request->input('cost_content'),
            'reward_content' => $request->input('reward_content'),
        ];
        QuestList::query()->where('id', $id)->update($param);
        return response()->redirectTo(route('m.getcoin.admin.quest_list.index'));
    }

}
