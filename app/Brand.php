<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Brand extends Base
{
    protected $table = "fb_brand";

    public function brandSelect()
    {
        return $this->get()->paginate( 3 );
    }

    public function checkExistsById( $id )
    {
        if( !$id ) return false;
        $info = Brand::where( 'id' , $id )->get()->count();
        if( $info ) {
            return true;
        }
        else {
            return false;
        }
    }

    public function getBrandInfoById( $id )
    {
        if( !$id ) return [];
        $info = Brand::where( 'id' , $id )->get();
        return $info;
    }

    public function getBrandHash()
    {
        $brandList = Brand::get( [ 'id' , 'brand_chinese_name' ] )->toArray();;
        $brandHash = [];
        if( $brandList ) {
            foreach( $brandList as $k => $v ) {
                $brandHash[ $v[ 'id' ] ] = $v[ 'brand_chinese_name' ];
            }
        }
        return $brandHash;
    }
}
