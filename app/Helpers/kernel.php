<?php

use App\Models\User;
use Modules\Coin\Services\BalanceChangeService;
use Modules\Core\Translate\TranslateExpression;

const project_name='dsy';

if (!function_exists('from')){
    //减少
    function from($data){
        $balanceChangeService = resolve(BalanceChangeService::class);
        $balanceChangeService->from($data['id'])
            ->withSymbol($data['symbol'])
            ->withNum($data['num'])
            ->withModule(project_name.$data['module'])
            ->withNo(0)
            ->withInfo(
                new TranslateExpression(project_name.'::message.'.$data['message'])
            )->change();
    }
}

if (!function_exists('to')){
    //增加
    function to($data){
        $balanceChangeService = resolve(BalanceChangeService::class);
        $balanceChangeService->to($data['id'])
            ->withSymbol($data['symbol'])
            ->withNum($data['num'])
            ->withModule(project_name.$data['module'])
            ->withNo(0)
            ->withInfo(
                new TranslateExpression(project_name.'::message.'.$data['message'])
            )->change();
    }
}

if (!function_exists('exception')){
    //异常
    function exception($message){
         throw new \Exception(trans(project_name.'::message.'.$message));
    }
}

if (!function_exists('HashMake')){
    //laravel的加密方式
    function HashMake($value){
        return Hash::make($value);
    }
}
if (!function_exists('HashCheck')){
    //laravel的验证密码方式
    function HashCheck($password,$passwords){
        return Hash::check($password,$passwords);
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
