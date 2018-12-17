<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Shop extends Model
{
    protected $table = "fb_shop";

    //
    public function checkExistsByWhere( $where )
    {
        if( !$where || !is_array( $where ) ) return false;
        $info = Shop::where( $where )->get()->count();
        if( $info ) {
            return true;
        }
        else {
            return false;
        }
    }

    public function getShopInfoByWhere( $where )
    {
        if( !$where || !is_array( $where ) ) return [];
        $info = Shop::where( $where )->first();
        return $info;
    }
}
