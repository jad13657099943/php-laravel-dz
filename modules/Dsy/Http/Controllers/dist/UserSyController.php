<?php


namespace Modules\Dsy\Http\Controllers\dist;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Dsy\Models\Dsy\Chain;
use Modules\Dsy\Models\Message;
use Modules\Dsy\Models\Order;
use Modules\Dsy\Models\ProjectUser;
use Modules\Dsy\Models\Release;
use Modules\Dsy\Models\Team;
use Modules\Dsy\Models\Teams;
use Modules\Dsy\Models\Ttime;
use Modules\Dsy\Models\UserGrade;
use Modules\Dsy\Models\UserSy;
use Modules\Dsy\Models\UserSys;
use Modules\Dsy\Services\ReleaseService;
use Modules\Dsy\Services\UserSyService;
use Monolog\Handler\IFTTTHandler;
use Spatie\TranslationLoader\TranslationLoaders\Db;

class UserSyController extends Controller
{



    /**
     * 每日释放
     */
    public function Release()
    {
        $service = resolve(UserSyService::class);
        $service->Release();
    }

    /**
     * 线性释放模板
     */
    public function xianRelease()
    {
        $service = resolve(UserSyService::class);
        $service->xianRelease();
    }

    /**
     * 每日释放团队
     */
    public function teamRelease()
    {
        $service = resolve(UserSyService::class);
        $service->teamRelease();
    }


}
