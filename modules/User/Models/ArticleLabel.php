<?php


namespace Modules\User\Models;


use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class ArticleLabel extends Model
{

    public $table = 'article_label';

    public $guarded = [];

   /* public $translatable = ['name'];*/

   /* public function toArray()
    {
        $attributes = parent::toArray();

        foreach ($this->getTranslatableAttributes() as $name) {
            $attributes[$name] = $this->$name;
        }

        return $attributes;
    }*/
}
