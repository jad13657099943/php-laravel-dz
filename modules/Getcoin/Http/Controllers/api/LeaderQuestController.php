<?php


namespace Modules\Getcoin\Http\Controllers\api;


use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Getcoin\Models\QuestDistribute;
use Modules\Getcoin\Models\QuestList;
use Modules\Getcoin\Models\QuestReward;
use Modules\Getcoin\Models\QuestUserEnroll;
use Modules\Getcoin\Models\QuestUserFollow;
use Modules\Getcoin\Models\QuestUserSubmit;

/* 组长相应的任务 */

class LeaderQuestController extends Controller
{

    /**
     * 组长关注\取消关注 任务
     */
    public function followQuest(Request $request)
    {

        $id = $request->input('id');
        $userId = $request->user()->id;
        $type = $request->input('type', 0);

        $info = QuestUserFollow::query()->where('qid', $id)
            ->where('user_type', 'leader')
            ->where('user_id', $userId)
            ->first();
        if ($type == 0) {
            //取消关注
            if ($info) {
                $info->delete();
            }
        } else {
            //关注
            if (empty($info)) {

                $quest = QuestList::query()->where('id', $id)->first();
                if (empty($quest)) {
                    throw new \Exception('关注任务不存在');
                }
                $data = [
                    'qid' => $quest->id,
                    'did' => 0,
                    'user_id' => $userId,
                    'user_type' => 'leader',
                ];
                $model = new QuestUserFollow($data);
                $model->save();
            }
        }

        return ['msg' => '操作成功'];
    }

    //组长领取的分发任务
    public function distribute(Request $request)
    {

        $userId = $request->user()->id;
        $list = QuestDistribute::query()->where('user_id', $userId)
            ->with('quest')
            ->orderBy('id', 'desc')
            ->paginate($request->limit);

        foreach ($list as $item) {
            $item->type_text = $item->type;
            $item->state_text = $item->state;
        }

        return $list;
    }


    /**
     * 组长自己的任务分发详情
     */
    public function distributeInfo(Request $request)
    {

        $id = $request->input('id');
        $userId = $request->user()->id;
        $info = QuestDistribute::query()->where('id', $id)
            ->where('user_id', $userId)
            ->with('quest')
            ->first();

        if (empty($info)) {
            throw new \Exception('信息不存在');
        }
        return $info;
    }


    /**
     * 任务会员报名列表
     */
    public function enrollList(Request $request)
    {

        $did = $request->input('id');
        $list = QuestUserEnroll::query()->where('did', $did)
            ->with('user:id,username')
            ->orderBy('id', 'desc')
            ->paginate($request->limit);

        return $list;
    }


    /**
     * 会员提交的任务进度列表
     */
    public function submitList(Request $request)
    {

        $did = $request->input('id');
        $list = QuestUserSubmit::query()->where('did', $did)
            ->with('user:id,username')
            ->orderBy('id', 'desc')
            ->paginate($request->limit);

        foreach ($list as $item) {
            $item->state_text = $item->state;
        }

        return $list;
    }

    /**
     * 审核提交的任务进度
     * 通过之后，给会员一定的奖励
     */
    public function submitState(Request $request)
    {

        $id = $request->input('id');
        $userId = $request->user()->id;
        $state = $request->input('state');
        $money = $request->input('money');
        $remark = $request->input('remark');
        if (!$state || $money < 0) {
            throw new \Exception('参数填写错误');
        }

        $info = QuestUserSubmit::query()->where('state', 0)->find($id);
        if (empty($info)) {
            throw new \Exception('该任务已经审核过了');
        }

        //严格判断只有组长才能审核该提交,防止别人用非正常流程审核
        $distribute = QuestDistribute::query()->where('id', $info->did)->first();
        if (!$distribute || $distribute->user_id != $userId) {
            throw new \Exception('您没有权限审核该任务进度');
        }

        $info->state = $state;
        $info->reward = $money;
        $info->remark = $remark;
        \DB::transaction(function () use ($info, $distribute) {

            if ($info->state == 1) {
                //审核通过，给奖励
                $reward = [
                    'qid' => $distribute->qid,
                    'did' => $distribute->id,
                    'eid' => $info->eid,
                    'sid' => $info->id,
                    'send_uid' => $distribute->user_id, //组长出钱
                    'get_uid' => $info->user_id, //会员得到奖励
                    'money' => $info->reward,
                    'currency' => $distribute->currency,
                    'remark' => $info->remark,
                ];
                $model = new QuestReward($reward);
                $model->save();

            } elseif ($info->state != -1) {
                //审核不通过，直接更新状态
                throw new \Exception('审核状态有误');
            }

            $info->save();
            return $info;
        });

        return ['msg' => '操作成功'];

    }


    public function index()
    {

    }

}
