<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Modules\Coin\Jobs\CheckTradeJob;
use Modules\Coin\Jobs\SyncCoinPriceJob;
use Modules\Coin\Jobs\SyncTradeStateJob;
use Modules\Coin\Services\TokenioNoticeService;
use Modules\Coin\Services\TradeService;
use Modules\Dsy\Http\Controllers\dsy\CurlController;
use Modules\Dsy\Http\Controllers\dsy\ReleaseController;
use Modules\Dsy\Http\Controllers\dsy\TotalController;
use Modules\Dsy\Http\Controllers\unite\UniteController;
use Modules\Dsy\Http\Controllers\unite\UniteReleaseController;
use Modules\Dsy\Services\PartnerController;
use Modules\Dsy\Services\ReleaseService;
use Modules\Otc\Console\CancelExpireOtcTrade;
use Modules\TianYuan\Jobs\FundIssueStartJob;
use Modules\Dsy\Http\Controllers\dist\TestController;
use Modules\Dsy\Http\Controllers\dist\UserSyController;
use Modules\Dsy\Http\Controllers\dist\ZhiController;
use Modules\Dsy\Models\Message;
use Modules\Dsy\Models\UserSy;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param \Illuminate\Console\Scheduling\Schedule $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->call(function (){
            echo '更新换率';
            $url='https://data.gateapi.io/api2/1/ticker/fil_usdt';
            $data=curl($url);
            Message::query()->update(['last'=>$data['last']]);
        })->everyTwoMinutes();

       /* $schedule->call(function (ZhiController $controller,CurlController $curlController){
           // $controller->index();
         //   $curlController->curl_fil();
          //  $curlController->curl_star();
           // $curlController->curl_xch();
           // $controller->index();
        })->everyTwoMinutes();*/

      /*  $schedule->call(function (ReleaseController $controller){
            $controller->index();
        })->dailyAt('1:02');*/

    /*    $schedule->call(function (UserSyController $controller){
            $controller->xianRelease();
        })->dailyAt('1:04');*/

        $schedule->call(function (UserSyController $controller){
            $controller->release();
        })->dailyAt('23:51');

        $schedule->call(function (UserSyController $controller){
            $controller->teamRelease();
        })->dailyAt('23:53');

        $schedule->call(function (PartnerController $controller){
            $controller->index();
        })->dailyAt('23:55');

      /*  $schedule->call(function (UniteReleaseController $controller){
            $controller->release();
        })->dailyAt('1:12');

        $schedule->call(function (UniteReleaseController $controller){
            $controller->teamRelease();
        })->dailyAt('1:15');*/

        $schedule->call(function (TotalController $controller){
            $controller->total();
            $controller->totals();
        })->dailyAt('23:57');

      /*  $schedule->call(function (ReleaseController $controller){
            $controller->notRelease();
        })->dailyAt('1:19');*/

        $schedule->call(function (TestController $controller,TokenioNoticeService $service){
            $controller->tong($service);
        })->everyTwoMinutes();

        $schedule->call(function (TestController $controller,TradeService $service){
            $controller->tongs($service);
        })->everyTwoMinutes();

        $schedule->job(new SyncCoinPriceJob)
            ->withoutOverlapping()
            ->everyMinute();

        $schedule->job(new CheckTradeJob())
            ->withoutOverlapping()
            ->everyTwoMinutes();

        $schedule->job(new SyncTradeStateJob())
            ->withoutOverlapping()
            ->everyFiveMinutes();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
