<?php


namespace Modules\User\Http\Controllers\api;


use App\Models\User;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Routing\Controller;
use Modules\Core\Events\Frontend\UserBeforeLogin;
use Modules\Core\Http\Requests\Frontend\Auth\LoginRequest;
use Modules\Core\Models\Frontend\UserVerify;
use Modules\Core\Services\Frontend\UserLoginService;
use Modules\Core\Services\Frontend\UserRegisterService;
use Modules\Core\Services\Frontend\UserService;
use Modules\Core\Services\Frontend\UserVerifyService;
use Modules\User\Http\Requests\RegisterRequest;
use Modules\User\Models\ProjectUser;

/* 注册登录 */

class LoginController extends Controller
{

    use ThrottlesLogins;

    public function username()
    {
        return 'username';
    }

    /**
     * 登录
     * @param LoginRequest $request
     * @param UserLoginService $userLoginService
     * @param UserService $service
     * @return array|void
     * @throws \Illuminate\Validation\ValidationException
     */
    public function login(LoginRequest $request, UserService $service)
    {


        if ($this->hasTooManyLoginAttempts($request)) {

            $this->fireLockoutEvent($request);

            return $this->sendLockoutResponse();
        }

        try {

            $username = $request->input('username');
            $password = $request->input('password');
            $device = $request->input('device');
            $type = $request->input('type', 'user');
            $where['username'] = $username;
            $user = $service->one($where, [
                'exception' => false
            ]);
            if (empty($user)) {
                throw new \Exception('会员名不存在');
            }

            $isLeader = ProjectUser::query()->where('user_id', $user->id)->value('is_leader');
            if ($type == 'user' && $isLeader == 1) {
                throw new \Exception('您是组长，请从组长任务端口登录');
            }
            if ($type == 'headman' && $isLeader == 0) {
                throw new \Exception('您是会员，请从任务端口登录');
            }

            //验证码密码
            event(new UserBeforeLogin($user, User::LOGIN_TYPE_PASSWORD));
            $service->checkPassword($user, $password);

            $this->clearLoginAttempts($request);

            return [
                'access_token' => $user->createToken($request->device ?: 'frontend')->plainTextToken
            ];
        } catch (\Exception $e) {
            $this->incrementLoginAttempts($request);

            throw $e;
        }
    }


    /**
     * 注册
     * @param RegisterRequest $request
     * @param UserRegisterService $userRegisterService
     * @return \App\Models\User
     */
    public function register(RegisterRequest $request, UserRegisterService $userRegisterService)
    {

        return \DB::transaction(function () use ($request, $userRegisterService) {

            $username = $request->input('username'); //mobile || email
            $password = $request->input('password');
            $mobilePre = $request->input('mobile_pre', 86); //手机号国际区号
            $inviteCode = $request->input('invite_code'); //推荐码
            $code = $request->input('code'); //验证码

            if (filter_var($username, FILTER_VALIDATE_EMAIL)) {
                $userVerify = UserVerify::TYPE_EMAIL_REGISTER;
            } else {
                $userVerify = UserVerify::TYPE_MOBILE_REGISTER;
            }

            //验证短信验证码
            /** @var UserVerifyService $userService */
            $userService = resolve(UserVerifyService::class);
            $userService->getByKeyToken($username, $code, $userVerify, array_merge([
                'setExpired' => true, // 标记已使用
            ], $options['userVerifyOptions'] ?? []));


            $param = [
                'username' => $username,
                'password' => $password,
                'invite_code' => $inviteCode,
                'code' => $code
            ];


            //$user = $userRegisterService->register($request->validationData());
            //调用注册
            if (filter_var($username, FILTER_VALIDATE_EMAIL)) {
                $param['email'] = $username;
                $user = $userRegisterService->registerByEmail($param);
            } else {
                //手机号
                $param['mobile'] = $username;
                $user = $userRegisterService->registerByMobile($param);
            }


            $userInfo = $user->refresh();
            $userInfo->save();
            //添加到扩展表
            $parentFloor = 0;
            if ($user->inviter_id > 0) {
                $parentFloor = ProjectUser::query()->where('user_id', $user->inviter_id)
                    ->value('floor');
            }
            //记录自己所处层级
            $parentFloor += 1;

            $extendUser = [
                'user_id' => $userInfo->id,
                'parent_id' => $userInfo->inviter_id,
                'floor' => $parentFloor,
            ];

            $projectUserModel = new ProjectUser($extendUser);
            $projectUserModel->save();
            return $userInfo;
        });
    }


}
