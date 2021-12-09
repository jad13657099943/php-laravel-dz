<?php


namespace Modules\Getcoin\Models;


use Illuminate\Database\Eloquent\Model;

class UserBindAccount extends Model
{
    public $table = 'user_bind_account';
    public $guarded = [];

    protected $casts = [
        'param' => 'array',
    ];
}
