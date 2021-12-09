<?php


namespace Modules\Getcoin\Http\Requests;


use Illuminate\Foundation\Http\FormRequest;

class LeaderInfoRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'wechat' => ['required', 'string'],
            'wechat_group' => ['required', 'string'],
            'wechat_qrcode' => ['required', 'string'],
            'currency' => ['required', 'string'],
            'wallet_index' => ['required', 'integer', 'min:1'],
        ];
    }

    public function messages()
    {
        return [
            'wechat.required' => '微信号必填',
            'wechat_group.required' => '微信群名称必填',
            'wechat_qrcode.required' => '微信二维码图片必传',
            'currency.required' => '奖励币种必选',
            'wallet_index.required' => '需要生成的钱包地址数量必填，需大于0',
        ];

    }
}
