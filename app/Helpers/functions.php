<?php


use App\Models\User;
use Modules\Coin\Services\BalanceChangeService;
use Modules\Core\Translate\TranslateExpression;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;


if (! function_exists('fail')) {
    function fail($msg){
        return [
            'code'=>500,
            'msg'=>$msg
        ];
    }
}
if (!function_exists('from')){
    function from($data){
        $balanceChangeService = resolve(BalanceChangeService::class);
        $balanceChangeService->from($data['id'])
            ->withSymbol($data['symbol'])
            ->withNum($data['num'])
            ->withModule('coin.exchange_dec')
            ->withNo($data['mid'])
            ->withInfo(
                new TranslateExpression('dsy::message.兑换减少')
            )->change();
    }
}
if (!function_exists('buy')){
    function buy($data){
        $balanceChangeService = resolve(BalanceChangeService::class);
        $balanceChangeService->from($data['id'])
            ->withSymbol($data['symbol'])
            ->withNum($data['num'])
            ->withModule('zfil.buy')
            ->withNo($data['mid'])
            ->withInfo(
                new TranslateExpression('dsy::message.购买')
            )->change();
    }
}
if (!function_exists('free')){
    function free($data){
        $balanceChangeService = resolve(BalanceChangeService::class);
        $balanceChangeService->from($data['id'])
            ->withSymbol($data['symbol'])
            ->withNum($data['num'])
            ->withModule('zfil.free')
            ->withNo(0)
            ->withInfo(
                new TranslateExpression($data['message'])
            )->change();
    }
}
if (!function_exists('redeem')){
    function redeem($data){
        $balanceChangeService = resolve(BalanceChangeService::class);
        $balanceChangeService->to($data['id'])
            ->withSymbol($data['symbol'])
            ->withNum($data['num'])
            ->withModule('zfil.redeem')
            ->withNo(0)
            ->withInfo(
                new TranslateExpression('dsy::message.'.$data['message'])
            )->change();
    }
}
if (!function_exists('to')){
    function to($data){
        $balanceChangeService = resolve(BalanceChangeService::class);
        $balanceChangeService->to($data['id'])
            ->withSymbol($data['symbol'])
            ->withNum($data['num'])
            ->withModule('coin.exchange_inc')
            ->withNo($data['mid'])
            ->withInfo(
                new TranslateExpression('dsy::message.兑换增加')
            )->change();
    }
}
if (!function_exists('extracts')){
    function extracts($data){
        $balanceChangeService = resolve(BalanceChangeService::class);
        $balanceChangeService->to($data['id'])
            ->withSymbol($data['symbol'])
            ->withNum($data['num'])
            ->withModule('zfil.extract')
            ->withNo($data['mid'])
            ->withInfo(
                new TranslateExpression('dsy::message.'.$data['message'])
            )->change();
    }
}
if (!function_exists('team')){
    function team($data){
        $balanceChangeService = resolve(BalanceChangeService::class);
        $balanceChangeService->to($data['id'])
            ->withSymbol($data['symbol'])
            ->withNum($data['num'])
            ->withModule('zfil.team')
            ->withNo($data['mid'])
            ->withInfo(
                new TranslateExpression('dsy::message.矿池分红')
            )->change();
    }
}
if (!function_exists('mill')){
    function mill($data){
        $balanceChangeService = resolve(BalanceChangeService::class);
        $balanceChangeService->to($data['id'])
            ->withSymbol($data['symbol'])
            ->withNum($data['num'])
            ->withModule('zfil.mill')
            ->withNo($data['mid'])
            ->withInfo(
                new TranslateExpression('dsy::message.领取')
            )->change();
    }
}
//获取上级
if (!function_exists('pid')){
    function pid($id, $pids = [])
    {
        $pid = User::query()->where('id', $id)->value('inviter_id');
        if ($pid) {
            $pids[] = $pid;
            return pid($pid, $pids);
        }
        return $pids;
    }
}

if(! function_exists('success')) {
    function success($result, $message = 'ok')
    {
        return ['code' => 200, 'data' => $result, 'msg' => $message];
    }
}

if (!function_exists('curl')){
    function curl($url){
        $ch = curl_init();
        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch,CURLOPT_HEADER,0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1 );
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        $res = curl_exec($ch);
        curl_close($ch);
        $json_obj = json_decode($res,true);
        return $json_obj;
    }
}
if (!function_exists('curls')){
    function curls($url){
        $ch = curl_init();
        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch,CURLOPT_HEADER,0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1 );
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        $res = curl_exec($ch);
        curl_close($ch);

        return $res;
    }
}
if (!function_exists('token')){
    function token(){
            return rand(10000, 99999);
    }
}
if (!function_exists('ReturnCode')){
    function ReturnCode($state){
        if ($state){
            return[
                'code'=>200
            ];
        }else{
            return [
                'code'=>500
            ];
        }
    }
}
if (!function_exists('HashMake')){
    //laravel的加密方式
    function HashMake($value){
        return Hash::make($value);
    }
}
if (!function_exists('zhi')){
    function zhi($id){
        return User::query()->where('inviter_id',$id)->count();
    }
}
if (!function_exists('team')){
    function teams($id){
        $data= User::query()->where('inviter_id',$id)->pluck('id');
        $num=User::query()->where('inviter_id',$id)->count();
        foreach ($data as $datum){
            $num=$num+teams($datum);
        }
        return $num;
    }
}

//post请求
if (!function_exists('curl_post')){
    function curl_post($url,$post_data){
        $data_string = json_encode($post_data);
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS,$data_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Content-Length: ' . strlen($data_string))
        );
        curl_setopt($ch, CURLOPT_ENCODING, 'deflate');
        $result = curl_exec($ch);
        return json_decode($result,true);
    }
}

//邮件
if (!function_exists('Mails')){
     function Mails($email,$code){
        $mail = new PHPMailer(true);
        //smtp.qq.com
        //1198228864@qq.com
        //unywhlgppsgzjhed
        try {
            //Server settings
            //$mail->SMTPDebug = SMTP::DEBUG_SERVER;                      // Enable verbose debug output
            $mail->isSMTP();
            $mail->SingleTo = true;
            // Send using SMTP
            $mail->Host       = 'smtp.qq.com';   //谷歌配置smtp.gmail.com                 // Set the SMTP server to send through
            $mail->SMTPAuth   = true;                                   // Enable SMTP authentication
            $mail->Username   = '1198228864@qq.com';     //谷歌账号    internaltutorfriend@gmail.com            // SMTP username
            $mail->Password   = 'gdfzvkumrdzobafh';        //密码         Tutorfriend06              // SMTP password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;         // Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` encouraged
            $mail->Port       = 465;                                    // TCP port to connect to, use 465 for `PHPMailer::ENCRYPTION_SMTPS` above
            //Recipients
            $mail->setFrom('1198228864@qq.com', 'Tutorfriend');
            $mail->addAddress($email, 'Joe User');     // Add a recipient
            //$mail->addAddress('1198228864@qq.com');               // Name is optional
            $mail->addReplyTo('1198228864@qq.com', 'Information');
            // $mail->addCC('cc@example.com');
            // $mail->addBCC('bcc@example.com');
            // Attachments
            //$mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
            //$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name
            // Content
            $mail->isHTML(true);                                  // Set email format to HTML
            $mail->Subject = '你好，大正创投,敬上';
            $mail->Body    = '您的验证码'.$code;
            $mail->AltBody = '';
            $mail->send();
        } catch (Exception $e) {
             $mail->ErrorInfo;
        }
    }
}
