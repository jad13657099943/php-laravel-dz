<?php


namespace Modules\User\Services;

/*会员收款方式*/

use Modules\User\Models\UserPayment;

class PaymentService
{

    //保存收款设置
    public function savePayment($data)
    {

        if ($data['id'] ?? false) {
            //更新
            UserPayment::query()->where('id', $data['id'])->update($data);
        } else {
            $model = new UserPayment($data);
            $model->save();
        }

        return ['msg' => '操作成功'];
    }

}
