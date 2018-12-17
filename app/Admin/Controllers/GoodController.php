<?php

namespace App\Admin\Controllers;

use App\Goods;
use App\GoodsMapping;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

//use Symfony\Component\HttpFoundation\Cookie;

class GoodController extends Controller
{
    /*
     * 添加sku商品的信息
     * 并保存的cookie中
     */
    public function goodAdd( Request $request )
    {
        $info = $request->all();
        $res = DB::table( 'fb_goods' )->insert( [
            'product_id' => $info[ 'product_id' ] ,
            'original_goods_url' => $info[ 'original_goods_url' ] ,
            'goods_name' => $info[ 'goods_name' ] ,
            'market_price' => $info[ 'market_price' ] ,
            'original_goods_id' => $info[ 'original_goods_id' ] ,
            'shop_price' => $info[ 'shop_price' ] ,
            'stock_number' => $info[ 'stock_number' ] ,
            'goods_status' => $info[ 'goods_status' ] ,
            'orignal_website' => $info[ 'orignal_website' ]
        ] );

        $id = $id = DB::getPdo()->lastInsertId();

        $result = DB::table( "fb_goods_attribute_mapping" )->insert( [
            'goods_id' => $id ,
            'original_goods_id' => $info[ 'original_goods_id' ] ,
            'attribute_id' => $info[ 'attribute_name_id' ] ,
            'attribute_value_id' => $info[ 'attribute_value_id' ] ,
            'created_at' => date( 'Y-m-d H:i:s' , time() )
        ] );
    }

    /**
     * 忽略功能
     *
     * @param Request $request
     */
    public function goodPass( Request $request )
    {
        $data = \request()->all();

        $res = Goods::where( 'original_goods_id' , '=' , $data[ 'original_goods_id' ] )->delete();

        $res1 = GoodsMapping::where( 'original_goods_id' , '=' , $data[ 'original_goods_id' ] )->delete();
    }

    public function goodUpdate( Request $request )
    {
        $data = \request()->all();
        dd( $data );
    }
}
