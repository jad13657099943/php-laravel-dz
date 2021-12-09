<?php


namespace Modules\Dsy\Services;


use Modules\Core\Services\Frontend\UserService;
use Modules\Dsy\Models\CoinAsset;
use Modules\Dsy\Models\Dsy\Chain;
use Modules\Dsy\Models\Dsy\Restrict;
use Modules\Dsy\Models\UserGrade;

class PublicService
{

    /**
     * 验证密码
     * @param $uid
     * @param $password
     * @throws \Illuminate\Validation\ValidationException
     */
    public function check_password($uid, $password)
    {

        $userService = resolve(UserService::class);

        $userService->checkPayPassword($uid, $password);
    }

    /**
     * 验证余额
     * @param $uid
     * @param $symbol
     * @param $num
     * @throws \Exception
     */
    public function check_balance($uid, $symbol, $num)
    {

        $balance = CoinAsset::getBalance($uid, $symbol);

        if ($balance < $num) throw new \Exception(trans("dsy::message.余额不足"));
    }

    /**
     * 验证模块权限
     * @param $uid
     * @param $chain
     * @throws \Exception
     */
    public function check_restrict($uid, $chain)
    {
        $state = Restrict::getRestrict($uid, $chain);
        if ($state > 1) throw new \Exception(trans("dsy::message.暂无权限"));
    }

    /**
     * 生成等级
     * @param $uid
     */
    public function setGrade($uid)
    {
        $chainList = Chain::getChain();
        $data = [];
        $time = date('Y-m-d H:i:s');
        foreach ($chainList as $item) {
            $grade = UserGrade::getGrade($uid, $item);
            if (empty($grade)) $data[] = ['user_id' => $uid, 'chain' => $item, 'created_at' => $time];
        }
        UserGrade::starGrade($data);
    }

    /**
     * 生成权限
     * @param $uid
     */
    public function setRestrict($uid)
    {
        $chainList = Chain::getChain();
        $data = [];
        $time = date('Y-m-d H:i:s');
        foreach ($chainList as $item) {
            $restrict = Restrict::getRestrict($uid, $item);
            if (empty($restrict)) $data[] = ['user_id' => $uid, 'chain' => $item, 'created_at' => $time];
        }
        Restrict::starRestrict($data);
    }

    /**
     * 短信
     * @param $token
     * @param $mobile
     * @return string[]
     */
    public function smsbao($token, $mobile)
    {
        if ($token != '123456') {
            $statusStr = array(
                "0" => "短信发送成功",
                "-1" => "参数不全",
                "-2" => "服务器空间不支持,请确认支持curl或者fsocket，联系您的空间商解决或者更换空间！",
                "30" => "密码错误",
                "40" => "账号不存在",
                "41" => "余额不足",
                "42" => "帐户已过期",
                "43" => "IP地址限制",
                "50" => "内容含有敏感词"
            );
            $smsapi = "http://api.smsbao.com/";
            $user = "lrz413"; //短信平台帐号
            $pass = md5("Lsc4832671"); //短信平台密码
            $content = "您的验证码" . $token;//要发送的短信内容
            $phone = $mobile;//要发送短信的手机号码
            $sendurl = $smsapi . "sms?u=" . $user . "&p=" . $pass . "&m=" . $phone . "&c=" . urlencode($content);
            $result = file_get_contents($sendurl);
            return [
                'msg' => $statusStr[$result]
            ];
        }
    }
}
