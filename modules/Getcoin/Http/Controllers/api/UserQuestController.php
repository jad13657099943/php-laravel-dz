<?php


namespace Modules\Getcoin\Http\Controllers\api;


use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Getcoin\Models\QuestDistribute;
use Modules\Getcoin\Models\QuestReward;
use Modules\Getcoin\Models\QuestUserEnroll;
use Modules\Getcoin\Models\QuestUserFollow;
use Modules\Getcoin\Models\QuestUserSubmit;

class UserQuestController extends Controller
{

    /**
     * 会员报名的任务列表
     */
    public function userEnrollQuestList(Request $request)
    {

        $userId = $request->user()->id;
        $list = QuestUserEnroll::query()->where('user_id', $userId)
            ->with(['quest', 'distribute'])
            ->orderBy('id', 'desc')
            ->paginate($request->limit);

        return $list;
    }


    /**
     * 报名的任务的提交进度列表
     */
    public function questSubmitList(Request $request)
    {

        $userId = $request->user()->id;
        $eid = $request->input('eid');
        $list = QuestUserSubmit::query()->where('user_id', $userId)
            ->where('eid', $eid)
            ->paginate($request->limit);

        foreach ($list as $item) {
            $item->state_text = $item->state;
        }

        return $list;
    }

    /**
     * 报名的任务的奖励明细列表
     */
    public function questRewardList(Request $request)
    {

        $userId = $request->user()->id;
        $eid = $request->input('eid');
        $list = QuestReward::query()->where('get_uid', $userId)
            ->where('eid', $eid)
            ->paginate($request->limit);

        return $list;
    }


    /**
     * 关注的任务列表
     */
    public function followQuestList(Request $request)
    {

        $userId = $request->user()->id;
        $list = QuestUserFollow::query()->where('user_id', $userId)
            ->where('type', 'user')
            ->with(['quest', 'distribute'])
            ->orderBy('id', 'desc')
            ->paginate($request->limit);

        return $list;
    }


    /**
     * 关注\取消关注 任务
     */
    public function followQuest(Request $request)
    {

        $did = $request->input('id');
        $userId = $request->user()->id;
        $type = $request->input('type', 0);

        $info = QuestUserFollow::query()->where('did', $did)
            ->where('user_type', 'user')
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

                $distribute = QuestDistribute::query()->where('id', $did)->first();
                if (empty($distribute)) {
                    throw new \Exception('关注任务不存在');
                }
                $data = [
                    'qid' => $distribute->qid,
                    'did' => $did,
                    'user_id' => $userId,
                    'user_type' => 'user',
                ];
                $model = new QuestUserFollow($data);
                $model->save();
            }
        }

        return ['msg' => '操作成功'];
    }


}
