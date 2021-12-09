<?php


namespace Modules\Dsy\Models;


use Illuminate\Database\Eloquent\Model;

class Teams extends Model
{
    public $table = 'teams';
    public $guarded = [];


    public static function getTeams($chain){
       return self::query()->where('chain', $chain)->first();
    }
}
