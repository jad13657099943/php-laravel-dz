<?php


namespace Modules\User\Models;


use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class ProjectUser extends Model
{

    public $table = 'project_user';
    public $guarded = [];

    public static $gradeMap = [
        0 => '普通会员',
    ];

    public static $leaderNameMap = [
        0 => '会员',
        1 => '组长',
    ];

    public function getLeaderNameAttribute($value)
    {
        return self::$leaderNameMap[$value] ?? $value;
    }

    public function getGradeTextAttribute()
    {
        return self::$gradeMap[$this->attributes['grade']] ?? $this->attributes['grade'];
    }

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

}
