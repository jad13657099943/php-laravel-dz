<?php


namespace Modules\User\Http\Requests;


use Illuminate\Foundation\Http\FormRequest;

class SavePaymentRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'type' => ['required', 'string'],
            'value' => ['required', 'array'],
        ];
    }

    public function messages()
    {
        return [
            'type.required' => '请选择类型',
            'value.required' => '请填写具体参数',
            'value.array' => '请填写具体参数',
        ];
    }
}
