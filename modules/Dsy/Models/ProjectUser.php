<?php


namespace Modules\Dsy\Models;


use Illuminate\Database\Eloquent\Model;

class ProjectUser extends Model
{
    public $table = 'project_user';
    public $guarded = [];

    public static $state=[
        0=>'普通用户',
        1=>'VIP',
        2=>'经理',
        3=>'精英',
        4=>'总代',
        5=>'合伙人'
        ];
}
