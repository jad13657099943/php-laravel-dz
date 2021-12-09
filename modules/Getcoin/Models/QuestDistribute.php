<?php


namespace Modules\Getcoin\Models;


use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class QuestDistribute extends Model
{

    public $table = 'quest_distribute';
    public $guarded = [];

    protected $casts = [
        'picture' => 'array',
    ];

    public static $stateMap = [
        1 => '可分发',
        0 => '已结束'
    ];

    public function getStateTextAttribute($value)
    {
        return self::$stateMap[$value] ?? $value;
    }

    public function quest()
    {
        return $this->hasOne(QuestList::class, 'id', 'qid')->withDefault();
    }

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id')->withDefault();
    }
}
