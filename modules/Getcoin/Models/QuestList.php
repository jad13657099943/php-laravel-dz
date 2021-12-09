<?php


namespace Modules\Getcoin\Models;


use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class QuestList extends Model
{

    use HasTranslations;
    public $table = 'quest_list';
    public $guarded = [];
    public $translatable = ['title', 'content', 'image', 'unit','summary','cost_content','reward_content'];

    public function toArray()
    {
        $attributes = parent::toArray();

        foreach ($this->getTranslatableAttributes() as $name) {
            $attributes[$name] = $this->$name;
        }

        return $attributes;
    }

    public static $stateMap = [
        1 => '可分发',
        0 => '已结束'
    ];

    public static $isShowMap = [
        1 => '可显示',
        0 => '不显示'
    ];

    public function getStateTextAttribute($value)
    {
        return self::$stateMap[$value] ?? $value;
    }

    public function getIsShowTextAttribute($value)
    {
        return self::$isShowMap[$value] ?? $value;
    }

}
