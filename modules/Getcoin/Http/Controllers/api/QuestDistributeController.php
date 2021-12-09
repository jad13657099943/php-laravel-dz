<?php


namespace Modules\Getcoin\Http\Controllers\api;


use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Getcoin\Http\Requests\SubmitQuestRequest;
use Modules\Getcoin\Models\QuestDistribute;
use Modules\Getcoin\Models\QuestList;
use Modules\Getcoin\Models\QuestUserEnroll;
use Modules\Getcoin\Models\QuestUserFollow;
use Modules\Getcoin\Models\QuestUserSubmit;
use Modules\Getcoin\Services\UserService;

/* leader 生成的任务列表，给用户看 */

class QuestDistributeController extends Controller
{

    /**
     * 任务列表
     */
    public function list(Request $request)
    {

        $where = [];
        $userId = $request->user()->id;
        $type = $request->input('type', 'all');
        $qid = $request->input('qid', 0);
        $name = $request->input('name', '');
        $sortType = $request->input('sort_type', 'id_desc');

        switch ($type) {
            //历史任务(已完结状态)
            case 'history':
                $where[] = ['state', '=', '2'];
                //会员已报名的任务
                $didList = QuestUserEnroll::query()->where('user_id', $userId)->pluck('did');
                if ($didList->isNotEmpty()) {
                    $did = $didList->toArray();
                    $did = implode(',', $did);
                    $where[] = [\DB::raw('id in(' . $did . ')'), 1];
                } else {
                    $where[] = ['id', '=', 0];
                }
                break;
            //关注的任务(未完结状态的)
            case 'follow':
                $where[] = ['state', '<', '2'];
                $didList = QuestUserFollow::query()->where('user_id', $userId)
                    ->where('type','user')
                    ->pluck('did');
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
                $didList = QuestUserEnroll::query()->where('user_id', $userId)->pluck('did');
                if ($didList->isNotEmpty()) {
                    $did = $didList->toArray();
                    $did = implode(',', $did);
                    $where[] = [\DB::raw('id in(' . $did . ')'), 1];
                } else {
                    $where[] = ['id', '=', 0];
                }

                break;
            default:

                //获取自己所属的组长，只能查看自己组长发布的任务
                $userService = resolve(UserService::class);
                $leaderId = $userService->myLeader($userId);
                $where[] = ['user_id', '=', $leaderId];
                break;
        }


        if ($qid > 0) { //按母任务ID
            $where[] = ['qid', '=', $qid];
        }

        if ($name) {
            //搜索任务名称
            $qids = QuestList::query()->where('title', 'like', '%' . $name . '%')->pluck('id');
            if ($qids->isNotEmpty()) {

                $qids = $qids->toArray();
                $qids = implode(',', $qids);
                $where[] = [\DB::raw('id in(' . $qids . ')'), 1];
            } else {
                //搜索不到
                $where[] = ['qid', '=', 0];
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

        $list = QuestDistribute::query()->where($where)
            ->with('quest')
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
        $info = QuestDistribute::query()->where('id', $id)
            ->with('quest')
            ->first();
        if (empty($info)) {
            throw new \Exception('该信息已下架');
        }
        $info->type_text = $info->type;
        return $info;

    }


    /**
     * 会员报名任务
     * 会员领取组长发布的任务
     * 同一个任务，一个人只能领取1次
     */
    public function enroll(Request $request)
    {

        $did = $request->input('did');
        $userId = $request->user()->id;

        $info = QuestDistribute::query()->where('id', $did)
            ->where('state', 0)
            ->first();
        if (empty($info)) {
            throw new \Exception('该任务已下架');
        }
        if ($info->release_num >= $info->enroll_num) {
            throw new \Exception('该任务可报名次数剩余0次');
        }

        $log = QuestUserEnroll::query()->where('user_id', $userId)
            ->where('did', $did)
            ->first();
        if ($log) {
            throw new \Exception('该任务已经报名参加了');
        }

        return \DB::transaction(function () use ($info, $userId) {

            $data = [
                'qid' => $info->qid,
                'did' => $info->id,
                'user_id' => $userId,
            ];

            $model = new QuestUserEnroll($data);
            $model->save();

            //更新已报名次数
            $info->release_num = $info->release_num + 1;
            $info->save();
            return $model;
        });

    }

    /**
     * 会员提交任务
     * 同一个任务，可以提交多次
     * 提交之后，相关组长可以审核，审核通过才会给奖励
     * @param SubmitQuestRequest $request
     * @return QuestUserSubmit
     * @throws \Exception
     */
    public function submitQuest(SubmitQuestRequest $request)
    {

        $userId = $request->user()->id;
        $eid = $request->input('eid');

        //报名信息
        $enroll = QuestUserEnroll::query()->where('id', $eid)
            ->where('user_id', $userId)
            ->first();
        if (empty($enroll)) {
            throw new \Exception('您未领取该任务');
        }
        //任务信息
        $info = QuestDistribute::query()->where('id', $enroll->did)
            ->where('state', 0)
            ->first();
        if (empty($info)) {
            throw new \Exception('该任务已下架');
        }

        $data = [
            'qid' => $info->qid,
            'did' => $info->id,
            'eid' => $enroll->id,
            'user_id' => $userId,
            'state' => 0,
            'reward' => $request->input('reward'),
            'remark' => $request->input('remark'),
            'imgs' => $request->input('imgs'),
        ];


        //添加到任务提交表
        $model = new QuestUserSubmit($data);
        $model->save();
        return $model;
    }


}
