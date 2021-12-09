<?php

namespace Modules\Coin\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Modules\Core\Models\Traits\DynamicRelationship;
use Modules\Core\Models\Traits\HasFail;
use Modules\Core\Models\Traits\HasTableName;
use Modules\Core\Models\Casts\Translate;

class CoinLog extends Model
{
    use HasFail,
        HasTableName,
        DynamicRelationship;

    public $table = 'coin_log';

    protected $fillable = [
        'user_id',
        'symbol',
        'no',
        'module',
        'action',
        'num',
        'type',
        'info'
    ];

    protected $casts = [
      'info' => Translate::class
    ];

    protected $appends = [];

    public function getNumAttribute($num)
    {
        return floatval($num);
    }

    public function getModuleActionNameAttribute()
    {

        $coinLogModule = $this->coinLogModule()->where('action', $this->attributes['action'])->first();
        if($coinLogModule){
            return empty($coinLogModule) ? "" : $coinLogModule['title'];
        }else{
            return "";
        }

    }

    public function coinLogModule()
    {
        return $this->belongsTo(CoinLogModules::class, 'module', 'module')
            ->where('action', $this->action);
    }

    /**
     * 关联会员
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }
}
