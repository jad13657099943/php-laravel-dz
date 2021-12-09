<?php


namespace Modules\Getcoin\Models;


use Illuminate\Database\Eloquent\Model;

class QuestWalletTrack extends Model
{

    public $table = 'quest_wallet_track';
    public $guarded = [];

    public function wallet()
    {
        return $this->hasOne(QuestWallet::class, 'id', 'wallet_id')->withDefault();
    }

}
