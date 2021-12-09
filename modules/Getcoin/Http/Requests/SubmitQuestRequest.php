<?php


namespace Modules\Getcoin\Http\Requests;


use Illuminate\Foundation\Http\FormRequest;

class SubmitQuestRequest extends FormRequest
{

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'eid' => ['required', 'integer', 'min:1'],
            'reward' => ['required', 'numeric', 'min:0'],
            'remark' => ['required', 'string'],
            'imgs' => ['required'],
        ];
    }

    public function messages()
    {
        return [
            'eid.required' => '任务ID必填',
            'reward.required' => '预期奖励金额不能小于0',
            'reward.numeric' => '预期奖励金额不能小于0',
            'remark.required' => '请填写完成说明',
            'remark.string' => '请填写完成说明',
            'imgs.required' => '请上传图片',
            'imgs.array' => '请上传图片',
        ];

    }

}
