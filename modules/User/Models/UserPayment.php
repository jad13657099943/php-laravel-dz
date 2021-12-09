<?php


namespace Modules\User\Models;


use Illuminate\Database\Eloquent\Model;

class UserPayment extends Model
{

    protected $table = 'user_payment';

    protected $guarded = [];

    protected $casts = [
        'param' => 'array',
    ];
}
