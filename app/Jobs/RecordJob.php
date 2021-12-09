<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Modules\Dsy\Http\Controllers\dist\OrderController;
use Modules\Dsy\Models\Teams;
use Modules\Dsy\Models\UserGrade;
use Modules\Dsy\Services\OrderService;

class RecordJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected  $name;
    public $timeout = 60;
    public $tries = 2;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($name)
    {
        $this->name=$name;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(OrderService $controller)
    {
        $data=$this->name;
        \DB::transaction(function ()use($data,$controller){
            $teams = Teams::getTeams($data['chain']);
            $controller->up($data['uid'], $data['chain'], $teams,$data['money'],$data['symbol'],$data['order_id']);
            $grade = UserGrade::getGrade($data['uid'], $data['chain']);
            $controller->upDai($data['uid'], $grade, $data['chain'], $teams);
        });

        var_dump($data);
    }
}
