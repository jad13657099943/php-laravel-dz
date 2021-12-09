<?php


namespace Modules\Getcoin\Http\Controllers\admin;


use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Getcoin\Models\QuestList;

class QuestListController extends Controller
{

    public function index()
    {

        return view('getcoin::admin.quest_list.index');
    }

    public function create()
    {

        return view('getcoin::admin.quest_list.create',[
            'info' => QuestList::query()->newModelInstance(),
            'url' => 'm.getcoin.api.admin.api.quest_list.create',
            'id'=> 0
        ]);
    }

    public function editInfo(Request $request)
    {

        $id = $request->input('id');
        $info = QuestList::query()->find($id);
        return view('getcoin::admin.quest_list.create', [
            'info' => $info,
            'url' => 'm.getcoin.api.admin.api.quest_list.edit_info',
            'id'=> $info->id,
        ]);
    }

}
