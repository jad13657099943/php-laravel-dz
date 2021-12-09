<?php


namespace Modules\Getcoin\Http\Controllers\api;

/* 任务列表 */

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Getcoin\Http\Requests\QuestEditByLeaderRequest;
use Modules\Getcoin\Models\QuestDistribute;
use Modules\Getcoin\Models\QuestList;
use Modules\Getcoin\Models\QuestUserFollow;
use Modules\Getcoin\Models\UserLeader;

class QuestListController extends Controller
{

    /**
     * 任务列表
     */
    public function list(Request $request)
    {

        $userId = $request->user()->id;
        $type = $request->input('type', 'all');
        $name = $request->input('name', '');
        $sortType = $request->input('sort_type', 'id_desc');

        switch ($type) {
            //历史任务(已完结状态)
            case 'history':
                $where[] = ['state', '=', '0'];
                break;
            //关注的任务(未完结状态的)
            case 'follow':
                $where[] = ['state', '<', '2'];
                $didList = QuestUserFollow::query()->where('user_id', $userId)
                    ->where('user_type', 'leader')
                    ->pluck('qid');
                if ($didList->isNotEmpty()) {
                    $did = $didList->toArray();
                    $did = implode(',', $did);
                    $where[] = [\DB::raw('id in(' . $did . ')'), 1];
                } else {
                    $where[] = ['id', '=', 0];
                }

                break;
            //参与中的（未完结的）
            case 'participating':
                $where[] = ['state', '<', '2'];
                //会员已报名的任务
                $qidList = QuestDistribute::query()->where('user_id', $userId)
                    ->where('state', '<', 2)
                    ->pluck('qid');
                if ($qidList->isNotEmpty()) {
                    $qid = $qidList->toArray();
                    $qid = implode(',', $qid);
                    $where[] = [\DB::raw('id in(' . $qid . ')'), 1];
                } else {
                    $where[] = ['id', '=', 0];
                }

                break;
            default:
                $where[] = ['is_show', '=', 1];
                break;
        }


        if ($name) {
            //搜索任务名称
            $ids = QuestList::query()->where('title', 'like', '%' . $name . '%')->pluck('id');
            if ($ids->isNotEmpty()) {

                $ids = $ids->toArray();
                $ids = implode(',', $ids);
                $where[] = [\DB::raw('id in(' . $ids . ')'), 1];
            } else {
                //搜索不到
                $where[] = ['id', '=', 0];
            }
        }


        //判断排序方式(字段名+排序方式)
        $sortType = explode('_', $sortType);
        if (count($sortType) == 2) {
            $orderByField = $sortType[0];
            $orderByType = $sortType[1];
        } elseif (count($sortType) == 3) {
            $orderByField = $sortType[0] . '_' . $sortType[1];
            $orderByType = $sortType[2];
        } else {
            $orderByField = 'id';
            $orderByType = 'desc';
        }


        $list = QuestList::query()->where($where)
            ->orderBy($orderByField, $orderByType)
            ->paginate($request->limit);

        foreach ($list as $item) {
            $item->type_text = $item->type;
        }
        return $list;
    }

    /**
     * 任务详情
     */
    public function info(Request $request)
    {

        $id = $request->input('id');
        $info = QuestList::query()->where('state', 1)
            ->where('is_show', 1)
            ->where('id', $id)
            ->first();
        if (empty($info)) {
            throw new \Exception('该信息已下架');
        }

        $info->type_text = $info->type;
        return $info;
    }


    /**
     * 添加、编辑任务到组长分发列表
     */
    public function editByLeader(QuestEditByLeaderRequest $request)
    {

        $userId = $request->user()->id;
        $id = $request->input('id');
        $leader = UserLeader::query()->where('user_id', $userId)->first();
        if (empty($leader)) {
            throw new \Exception('您还未成为Leader组长');
        }
        $quest = QuestList::query()->find($id);
        if (empty($quest)) {
            throw new \Exception('该任务不下架');
        }


        $data = [
            'qid' => $id,
            'user_id' => $userId,
            'money' => $request->input('money'),
            'currency' => $leader->currency,
            'reward_content' => $request->input('reward_content'),
            'unit' => $request->input('unit'),
            'state' => $request->input('state', 0),
            'type' => $quest->type,
        ];

        $info = QuestDistribute::query()->where('user_id', $userId)
            ->where('qid', $id)
            ->first();

        if ($info) {
            QuestDistribute::query()->where('id', $info['id'])
                ->update($data);
        } else {
            $model = new QuestDistribute($data);
            $model->save();
        }

        return ['msg' => '操作成功'];
    }

}
