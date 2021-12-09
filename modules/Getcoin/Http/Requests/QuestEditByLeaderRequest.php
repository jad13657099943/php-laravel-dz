<?php


namespace Modules\Getcoin\Http\Requests;


use Illuminate\Foundation\Http\FormRequest;

class QuestEditByLeaderRequest extends FormRequest
{

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'id' => ['required', 'integer', 'min:1'],
            'money' => ['required', 'numeric', 'min:0'],
            'reward_content' => ['required', 'string'],
            'unit' => ['required', 'string'],
        ];
    }

    public function messages()
    {
        return [
            'qid.required' => '任务ID必填',
            'money.required' => '每单位奖励金额不能小于0',
            'money.numeric' => '每单位奖励金额不能小于0',
            'reward_content.required' => '奖励说明必填',
            'reward_content.string' => '奖励说明必填',
            'unit.required' => '单位说明必填',
            'unit.string' => '单位说明必填',
        ];

    }

}
