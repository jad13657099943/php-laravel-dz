<?php


namespace Modules\Dsy\Services;


use App\Models\User;
use Modules\Core\Services\Frontend\UserRegisterService;
use Modules\Core\Services\Traits\HasQuery;
use Modules\Dsy\Models\Order;
use Modules\Dsy\Models\ProjectUser;

use Modules\Dsy\Models\UserVerifies;


class UserService
{
    use HasQuery {
        one as queryOne;
        getById as queryGetById;
    }

    public $model;

    public function __construct(User $model)
    {
        $this->model = $model;
    }

    /**
     * 注册
     * @param $params
     * @param UserRegisterService $userRegisterService
     * @param \Modules\Core\Services\Frontend\UserService $userService
     * @param PublicService $publicService
     * @return mixed
     */
    public function register($params, UserRegisterService $userRegisterService, \Modules\Core\Services\Frontend\UserService $userService, PublicService $publicService)
    {
        return \DB::transaction(function () use ($userService, $userRegisterService, $params, $publicService) {
            if (!filter_var($params['mobile'], FILTER_VALIDATE_EMAIL)) {
                $isMobile = $userService->one(['mobile' => $params['mobile']], ['exception' => false]);
                if (!empty($isMobile)) throw new \Exception(trans("dsy::message.手机已注册"));
                $userInfo = $userRegisterService->registerByMobile($params);
                if (empty($userInfo['id'])) throw new \Exception(trans("dsy::message.注册失败"));
                $data = ['user_id' => $userInfo['id'], 'created_at' => date('Y-m-d H:i:s')];
                ProjectUser::query()->insert($data);
                $publicService->setGrade($userInfo['id']);
                // $publicService->setRestrict($userInfo['id']);
                return [
                    'code' => 200
                ];
            } else {
                $isMobile = $userService->one(['email' => $params['mobile']], ['exception' => false]);
                if (!empty($isMobile)) throw new \Exception(trans("dsy::message.邮箱已注册"));
                $params['email'] = $params['mobile'];
                unset($params['mobile']);
                $userInfo = $userRegisterService->registerByEmail($params);
                if (empty($userInfo['id'])) throw new \Exception(trans("dsy::message.注册失败"));
                $data = ['user_id' => $userInfo['id'], 'created_at' => date('Y-m-d H:i:s')];
                ProjectUser::query()->insert($data);
                $publicService->setGrade($userInfo['id']);
                // $publicService->setRestrict($userInfo['id']);
                return [
                    'code' => 200
                ];
            }
        });
    }

    /**
     * 登录
     * @param $params
     * @param \Modules\Core\Services\Frontend\UserService $service
     * @param PublicService $publicService
     * @return array
     * @throws \Illuminate\Validation\ValidationException
     */
    public function login($params, \Modules\Core\Services\Frontend\UserService $service, PublicService $publicService)
    {
        if (filter_var($params['account'], FILTER_VALIDATE_EMAIL)) {
            $isEmail = $service->one(['email' => $params['account']], ['exception' => false]);
            if (empty($isEmail)) throw new \Exception(trans("dsy::message.账户不存在"));
            $service->checkPassword($isEmail, $params['password']);
            //  $publicService->setRestrict($isEmail['id']);
            $publicService->setGrade($isEmail['id']);
            $token = $isEmail->createToken('frontend')->plainTextToken;
        } else {
            $isMobile = $service->one(['mobile' => $params['account']], ['exception' => false]);
            if (empty($isMobile)) throw new \Exception(trans("dsy::message.账户不存在"));
            $service->checkPassword($isMobile, $params['password']);
            $token = $isMobile->createToken('frontend')->plainTextToken;
        }
        return [
            'access_token' => $token
        ];
    }

    /**
     * 手机验证码登录
     * @param $mobile
     * @param $code
     * @return array
     * @throws \Exception
     */
    public function codeLogin($mobile, $code)
    {

        $where[] = ['key', '=', $mobile];
        $where[] = ['token', '=', $code];
        $where[] = ['type', '=', 'login'];
        $where[] = ['expired_at', '>=', date('Y-m-d H:i:s')];
        $user = UserVerifies::query()->where($where)->first();
        if (empty($user)) {
            throw new \Exception(trans("dsy::message.登录失败"));
        } else {
            $isMobile = $this->one(['mobile' => $mobile], ['exception' => false]);
            if (empty($isMobile)) throw new \Exception(trans("dsy::message.请注册"));
            $token = $isMobile->createToken('frontend')->plainTextToken;
            return [
                'access_token' => $token
            ];
        }
    }

    /**
     * 手机验证码
     * @param $params
     * @param $service
     * @param int $uid
     * @return int[]
     */
    public function getMobile($params, $service, $uid = 0)
    {

        $userVer = UserVerifies::query()->where('key', $params['mobile'])->first();
        $create_at = date('Y-m-d H:i:s');
        $expired_at = date('Y-m-d H:i:s', strtotime($create_at) + 300);
        $token = token();
        $env = env('NEW_CODE');
        if (!empty($env)) {
            $token = $env;
        }
        $data = ['key' => $params['mobile'], 'type' => $params['type'], 'token' => $token, 'created_at' => $create_at, 'expired_at' => $expired_at];
        if ($uid > 0) {
            $data = ['user_id' => $uid, 'key' => $params['mobile'], 'type' => $params['type'], 'token' => $token, 'created_at' => $create_at, 'expired_at' => $expired_at];
        }
        if (empty($userVer)) {
            UserVerifies::query()->insert($data);
        } else {
            UserVerifies::query()->where('key', $params['mobile'])->update($data);
        }
        $service->smsbao($token, $params['mobile']);
        return [
            'code' => 200
        ];
    }

    /**
     * 邮箱验证码
     * @param $params
     * @return int[]
     */
    public function getEmail($params)
    {
        $userVer = UserVerifies::query()->where('key', $params['email'])->first();
        $create_at = date('Y-m-d H:i:s');
        $expired_at = date('Y-m-d H:i:s', strtotime($create_at) + 300);
        $token = token();
        $env = env('NEW_CODE');
        if (!empty($env)) {
            $token = $env;
        }
        if (empty($userVer)) {
            UserVerifies::query()->insert(['key' => $params['email'], 'type' => $params['type'], 'token' => $token, 'created_at' => $create_at, 'expired_at' => $expired_at]);
        } else {
            UserVerifies::query()->where('key', $params['email'])->update(['token' => $token, 'type' => $params['type'], 'created_at' => $create_at, 'expired_at' => $expired_at]);
        }
        if ($token != '123456') {
            Mails($params['email'], $token);
            return [
                'msg' => 200
            ];
        }
        return [
            'msg' => 200
        ];
    }

    /**
     * 订单详情
     * @param $params
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|object|null
     */
    public function getOrderDetail($params)
    {

        $list = Order::query()->where('id', $params['id'])->first();
        $list['content'] = json_decode($list['content'], true);
        $list['state'] = Order::$state[$list['state']];
        $list['type'] = Order::$type[$list['type']];
        return $list;
    }

    /**
     * 修改支付密码
     * @param $params
     * @param $user
     * @return string[]
     * @throws \Exception
     */
    public function setPatPassword($params, $user)
    {
        $token = UserVerifies::query()->where('key', $params['mobile'])->where('type', 'pay_password_reset')->where('expired_at', '>', date('Y-m-d H:i:s'))->value('token');
        if ($token == $params['code']) {
            User::query()->where('id', $user['id'])
                ->where('mobile', $params['mobile'])->update(['pay_password' => HashMake($params['password'])]);
            return ['msg' => '修改成功'];
        } else {
            throw new \Exception(trans('dsy::message.验证码错误'));
        }
    }

}
