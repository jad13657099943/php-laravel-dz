<?php


namespace Modules\Dsy\Models\Dsy;


use Illuminate\Database\Eloquent\Model;
use Modules\Dsy\Models\Kernel;
use function Symfony\Component\String\s;

class Chain extends Kernel
{
    public $table = 'dsy_chain';

    public $guarded = [];

    public static $state = [
        'FIL' => 'FIL',
        'STAR' => 'FILESTAR',
        'CHIA' => 'XCH',
        'UNITE' => 'FIL'
    ];

    public static function getChain()
    {
        return self::query()->pluck('chain');
    }
}
