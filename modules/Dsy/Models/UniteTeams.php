<?php


namespace Modules\Dsy\Models;


use Illuminate\Database\Eloquent\Model;

class UniteTeams extends Model
{
    public  $table='dsy_unite_teams';

    public $guarded=[];

    public static function getTeams(){

        return self::query()->first();
    }
}
