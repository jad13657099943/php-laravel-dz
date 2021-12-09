<?php


namespace Modules\Getcoin\Models;


use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class QuestUserEnroll extends Model
{

    public $table = 'quest_user_enroll';
    public $guarded = [];

    public function quest()
    {
        return $this->hasOne(QuestList::class, 'id', 'qid')->withDefault();
    }

    public function distribute()
    {
        return $this->hasOne(QuestDistribute::class, 'id', 'did')->withDefault();
    }

    public function user(){
        return $this->hasOne(User::class,'id','user_id')->withDefault();
    }
}
