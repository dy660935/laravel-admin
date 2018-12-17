<?php

namespace App;

use Moloquent;
use DB;

class Mongodb extends Moloquent
{
    protected $collection = 'kl';
    protected $connection = 'kaola';

    public static function test()
    {

        $users = DB::all();
        dd( $users );
    }
}