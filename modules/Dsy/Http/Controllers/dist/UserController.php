<?php


namespace Modules\Dsy\Http\Controllers\dist;


use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Core\Http\Requests\Frontend\Auth\VerifyMobileRequest;
use Modules\Core\Services\Frontend\NotificationService;
use Modules\Core\Services\Frontend\UserRegisterService;
use Modules\Core\Services\Frontend\UserService;
use Modules\Dsy\Models\Order;
use Modules\Dsy\Models\ProjectUser;
use Modules\Dsy\Models\Record;
use Modules\Dsy\Models\Team;
use Modules\Dsy\Models\User;
use Modules\Dsy\Models\UserInvitationTree;
use Modules\Dsy\Models\UserVerifies;
use MediaUploader;
use Modules\Dsy\Services\PublicService;
use Modules\Dsy\Services\UserSyService;

class UserController extends Controller
{

    /**
     * 注册
     * @param Request $request
     * @param UserRegisterService $userRegisterService
     * @param UserService $userService
     * @param PublicService $publicService
     * @param \Modules\Dsy\Services\UserService $service
     * @return mixed
     */
    public function register(Request $request, UserRegisterService $userRegisterService, UserService $userService, PublicService $publicService, \Modules\Dsy\Services\UserService $service)
    {
        $params = $request->input();
        return $service->register($params, $userRegisterService, $userService, $publicService);
    }

    /**
     * 登录
     * @param Request $request
     * @param UserService $service
     * @param PublicService $publicService
     * @param \Modules\Dsy\Services\UserService $userService
     * @return array
     * @throws \Exception
     */
    public function login(Request $request, UserService $service, PublicService $publicService, \Modules\Dsy\Services\UserService $userService)
    {
        $params = $request->input();
        return $userService->login($params, $service, $publicService);
    }



    /**
     * 手机验证码登录
     * @param Request $request
     * @param \Modules\Dsy\Services\UserService $service
     * @return array
     * @throws \Exception
     */
    public function codeLogin(Request $request, \Modules\Dsy\Services\UserService $service)
    {
        $mobile = $request->mobile;
        $code = $request->code;
        return $service->codeLogin($mobile, $code);
    }



    /**
     * 手机验证码
     * @param Request $request
     * @param \Modules\Dsy\Services\UserService $service
     * @param PublicService $publicService
     * @return int[]
     */
    public function getMobile(Request $request, \Modules\Dsy\Services\UserService $service, PublicService $publicService)
    {
        $params = $request->input();
        return $service->getMobile($params, $publicService);
    }


    /**
     * 手机验证码
     * @param Request $request
     * @param \Modules\Dsy\Services\UserService $service
     * @param PublicService $publicService
     * @return int[]
     */
    public function getMobiles(Request $request, \Modules\Dsy\Services\UserService $service, PublicService $publicService)
    {
        $params = $request->input();
        $user = $request->user();
        return $service->getMobile($params, $publicService, $user['id']);
    }

    /**
     * 邮箱验证码
     * @param Request $request
     * @param \Modules\Dsy\Services\UserService $service
     * @return int[]
     */
    public function getEmail(Request $request, \Modules\Dsy\Services\UserService $service)
    {
        $params = $request->input();
        return $service->getEmail($params);
    }

    /**
     * 用户信息
     * @param Request $request
     * @return mixed
     */
    public function user(Request $request)
    {
        return $request->user();
    }

    /**
     * 订单详情
     * @param Request $request
     * @param \Modules\Dsy\Services\UserService $service
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|object|null
     */
    public function getOrderDetail(Request $request, \Modules\Dsy\Services\UserService $service)
    {
        $params = $request->input();
        return $service->getOrderDetail($params);
    }


    /**
     * 修改支付密码
     * @param Request $request
     * @param \Modules\Dsy\Services\UserService $service
     * @return string[]
     * @throws \Exception
     */
    public function setPatPassword(Request $request, \Modules\Dsy\Services\UserService $service)
    {
        $user = $request->user();
        $params = $request->input();
        return $service->setPatPassword($params, $user);
    }


}
