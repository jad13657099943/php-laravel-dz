<?php


namespace Modules\Dsy\Models\Dsy;




use Modules\Dsy\Models\Kernel;

class Name extends Kernel
{
    public $table='dsy_name';
    public $guarded=[];

    public static $name=[
      1=>'one',
      2=>'a',
      3=>'b',
      4=>'c',
      5=>'d',
      6=>'e',
      7=>'f',
      8=>'g'
    ];

    public static $type=['one','a','b','c','d','e','f','g'];

    public static function getName(){
        return self::query()->first()->toArray();
    }
}
