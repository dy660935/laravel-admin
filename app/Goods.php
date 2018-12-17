<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Goods extends Base
{
    protected $table = "fb_goods";

    public function checkExistsByOriginalGoodsId( $original_goods_id )
    {
        if( !$original_goods_id ) return false;
        $info = Goods::where( 'original_goods_id' , $original_goods_id )->get()->count();
        if( $info ) {
            return true;
        }
        else {
            return false;
        }
    }

    public function checkExistsByWhere( $where )
    {
        if( !$where || !is_array( $where ) ) return [];
        $info = Goods::where( $where )->get()->count();
        if( $info ) {
            return true;
        }
        else {
            return false;
        }
    }

    public function setBestPrice($goodsId) {
        if(!$goodsId) return false;
        return Goods::where('id',$goodsId)->update(['is_best_price'=>1]);
    }

    public function getBestPriceByProductId($productId) {
        if(!$productId) return [];
        //$sql = "select fb_goods.id product_id, min(shop_price) sp from fb_goods where product_id = '{$productId}' group by product_id";
        $sql = "select id, product_id, shop_price from fb_goods where product_id = '{$productId}' order by shop_price asc limit 1";
        return DB::select($sql);
    }

    public function setBestPriceByProductId($productId) {
        if(!$productId) return false;
        return Goods::where(['product_id'=>$productId,'is_best_price'=>1])->update(['is_best_price'=>0]);
    }

    public function getGoodsMatchsListByProductId($productId) {
        if(!$productId) return [];
        $sql = "select g.id,category_id,brand_id,product_id,goods_name,good_specs,shop_price,g.orignal_website_id,product_image,original_goods_url,g.is_import,p.original_product_id,g.original_goods_id,g.currency_genre,is_master,original_category_name,p.id pid,p.product_keywords from fb_goods g left join fb_product p on p.original_product_id = g.original_goods_id where product_id = '{$productId}' order by p.is_master desc ";
        return DB::select($sql);
    }

    public function getGoodsMatchsListByGoodsId($goods_id) {
        if(!$goods_id) return [];
        $sql = "select p.id pid,g.id,category_id,brand_id,product_id,goods_name,good_specs,shop_price,g.orignal_website_id,product_image,original_goods_url,g.is_import,p.original_product_id,g.original_goods_id,g.currency_genre,is_master,p.product_keywords from fb_goods g left join fb_product p on p.original_product_id = g.original_goods_id where g.id = '{$goods_id}' order by p.is_master desc ";
        return DB::select($sql);
    }

    public function getProductsMatchsList($goodsIdAry) {
        if(!$goodsIdAry || !is_array($goodsIdAry)) return [];
        $goodsIdStr = implode(',',$goodsIdAry);
        $sql = "select g.id,category_id,brand_id,product_id,goods_name,good_specs,shop_price,g.orignal_website_id,product_image,original_goods_url,g.is_import,p.original_product_id,g.original_goods_id,g.currency_genre,original_category_name,p.id pid from fb_goods g left join fb_product p on p.original_product_id = g.original_goods_id where g.id in ({$goodsIdStr}) ";
        $sql .= "order by g.is_import asc, g.id desc";
        return DB::select($sql);
    }

    public function getGoodsMatchsList($goodsIdAry) {
        if(!$goodsIdAry || !is_array($goodsIdAry)) return [];
        $goodsIdStr = implode(',',$goodsIdAry);
        $sql = "select g.id,category_id,brand_id,product_id,goods_name,good_specs,shop_price,g.orignal_website_id,product_image,original_goods_url,g.is_import,p.original_product_id,g.original_goods_id,g.currency_genre,original_category_name from fb_goods g left join fb_product p on p.id = g.product_id where g.id in ({$goodsIdStr}) and p.original_product_id = g.original_goods_id ";
        $sql .= "order by g.is_import asc, g.id desc";
        return DB::select($sql);
    }

    public function getGoodsMatchsCountByWhere($whereAry) {
        if(!$whereAry || !is_array($whereAry)) return [];
        $where = '';
        if(isset($whereAry['brand_id'])) {
            $where .= " and brand_id = '{$whereAry['brand_id']}'";
        }
        if(isset($whereAry['category_id'])) {
            $where .= " and category_id = '{$whereAry['category_id']}'";
        }
        if(isset($whereAry['is_master'])) {
            $where .= " and p.is_master = '1'";
        }
        if(isset($whereAry['orignal_website_id']) && is_array($whereAry['orignal_website_id'])) {
            $websiteStr = implode(',',$whereAry['orignal_website_id']);
            $where .= " and g.orignal_website_id in ({$websiteStr})";
        }
        if($where) {
            $where = " where 1 ".$where;
        } else {
            return [];
        }
        $sql = "select count(*) countNum from fb_goods g left join fb_product p on p.id = g.product_id ".$where." and p.original_product_id = g.original_goods_id ";
        return DB::select($sql);
    }

    public function getGoodsMatchsListByWhere($whereAry) {
        if(!$whereAry || !is_array($whereAry)) return [];
        $where = '';
        if(isset($whereAry['brand_id'])) {
            $where .= " and brand_id = '{$whereAry['brand_id']}'";
        }
        if(isset($whereAry['category_id'])) {
            $where .= " and category_id = '{$whereAry['category_id']}'";
        }
        if(isset($whereAry['is_master'])) {
            $where .= " and p.is_master = '1'";
        }
        if(isset($whereAry['orignal_website_id']) && is_array($whereAry['orignal_website_id'])) {
            $websiteStr = implode(',',$whereAry['orignal_website_id']);
            $where .= " and g.orignal_website_id in ({$websiteStr})";
        }
        if($where) {
            $where = " where 1 ".$where;
        } else {
            return [];
        }
        $limit = " limit 20 ";
        if(isset($whereAry['limit'])) {
            $limit = ' limit '.$whereAry['limit'];
        }
        if(isset($whereAry['limit']) && isset($whereAry['limit'])) {
            $limit = " limit  {$whereAry['offset']},{$whereAry['limit']} ";
        }
        $sql = "select g.id,category_id,brand_id,product_id,goods_name,good_specs,shop_price,g.orignal_website_id,product_image,original_goods_url,g.is_import,p.original_product_id,g.original_goods_id,g.currency_genre,original_category_name from fb_goods g left join fb_product p on p.id = g.product_id ".$where." and p.original_product_id = g.original_goods_id ";
        $sql .= "order by g.is_import asc, g.id desc";
        $sql .= $limit;
        return DB::select($sql);
    }

    public function getGoodsIdByProductId($productId) {
        if(!$productId ) return [];
        $sql = "select g.id,product_id,g.orignal_website_id,g.is_import,p.original_product_id,g.original_goods_id from fb_goods g left join fb_product p on p.id = g.product_id where g.product_id in ({$productId})";
        return DB::select($sql);
    }

    public function getGoodsMatchsCount() {
        $sql = "select is_import,count(*) countNum from fb_goods group by is_import";
        return DB::select($sql);
    }
}
