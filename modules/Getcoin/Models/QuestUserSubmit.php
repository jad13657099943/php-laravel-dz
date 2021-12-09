<?php


namespace Modules\Getcoin\Models;


use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class QuestUserSubmit extends Model
{
    public $table = 'quest_user_submit';
    public $guarded = [];

    protected $casts = [
        'imgs' => 'array',
    ];

    public static $stateMap = [
        '-1' => '被驳回',
        '0' => '待审核',
        '1' => '已通过',
    ];

    public function getStateTextAttribute($value)
    {
        return self::$stateMap[$value] ?? $value;
    }

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id')->withDefault();
    }

}
