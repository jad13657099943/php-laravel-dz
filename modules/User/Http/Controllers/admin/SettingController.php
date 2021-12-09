<?php


namespace Modules\User\Http\Controllers\admin;


use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\View\View;

class SettingController extends Controller
{
    protected $schema = [
        // 基础奖励
        [
            'reward_recommend' => [
                'key' => 'reward_recommend',
                'type' => 'number',
                'title' => '直推奖励',
                'value' => '10',
                'description' => '%',
            ],
        ],
        // 其他。。。
    ];


    /**
     * @return Factory|View
     */
    public function index()
    {
        return view('user::admin.setting.index', [
            'configList' => $this->normalizeSchema(config('user::config', []), $this->schema[0]),
            //其他类型设置
            //'configList2' => $this->normalizeSchema(config('amb::config', []), $this->schema[1]),
        ]);
    }


    /**
     * @param array $data
     * @param array $config
     * @return array|array[]
     */
    protected function normalizeSchema(array $data, array $config)
    {
        return array_map(function ($value) use ($data) {
            return array_merge($value, [
                'value' => $data[$value['key']] ?? $value['value'],
            ]);
        }, $config);
    }


    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function update(Request $request)
    {
        $post = $request->post();
        unset($post['_token']);

        store_config('user::config', $post);

        return response()->redirectTo(route('m.user.admin.setting.index'));
    }
}
