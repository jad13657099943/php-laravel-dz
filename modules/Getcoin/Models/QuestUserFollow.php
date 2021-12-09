<?php


namespace Modules\Getcoin\Models;


use Illuminate\Database\Eloquent\Model;

class QuestUserFollow extends Model
{

    public $table = 'quest_user_follow';
    public $guarded = [];

    public function quest()
    {
        return $this->hasOne(QuestList::class, 'id', 'qid');
    }

    public function distribute()
    {
        return $this->hasOne(QuestDistribute::class, 'id', 'did');
    }
}
