<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use \App\Model;
use \App\Country;
use \App\Currency;
use Illuminate\Support\Facades\DB;
use \App\Goods;
use \App\Website;

class Product extends Model
{
    use Notifiable;

    protected $table = "fb_product";

    public $errMsg = '';

    public $websiteObj;

    public function checkExistsByOriginalProductId( $original_product_id )
    {
        if( !$original_product_id ) return false;
        $info = Product::where( 'original_product_id' , $original_product_id )->get()->count();
        if( $info ) {
            return true;
        }
        else {
            return false;
        }
    }

    public function getProductInfoByWhere( $where )
    {
        if( !$where || !is_array( $where ) ) return [];
        $info = Product::where( $where )->first();
        if( $info ) {
            return $info->toArray();
        }
        else {
            return [];
        }
    }

    public function getProductInfoByOriginalProductId( $original_product_id )
    {
        if( !$original_product_id ) return [];
        $info = Product::where( 'original_product_id' , $original_product_id )->first();
        if( $info ) {
            return $info->toArray();
        }
        else {
            return [];
        }
    }

    public function delMatch($productId,$goodsId) {
        if(!$productId ) return false;
        if(!$goodsId ) return false;
        DB::beginTransaction();
        if(!Product::where('id',$productId)->update(['is_import'=>1, 'product_status'=>2])) {
            DB::rollBack();
            return false;
        }
        if(!Goods::where('id',$goodsId)->update(['is_import'=>1, 'goods_status'=>2, 'product_id'=>$productId])) {
            DB::rollBack();
            return false;
        }
        DB::commit();
        return true;
    }

    public function doMatch($productIdAry,$productId,$goodsIdAry) {
        if(!$productId ) return false;
        if(!$productIdAry || !is_array($productIdAry)) return false;
        if(!$goodsIdAry || !is_array($goodsIdAry)) return false;
        DB::beginTransaction();
        if(!Product::where('id',$productId)->update(['is_master'=>1,'is_import'=>0, 'product_status'=>1])) {
            DB::rollBack();
            $this->errMsg = 'product_id';
            return false;
        }
        if(!Product::whereIn('id',$productIdAry)->update(['is_master'=>0,'is_import'=>0, 'product_status'=>2])) {
            DB::rollBack();
            $this->errMsg = 'product_id_ary';
            return false;
        }
        if(!Goods::whereIn('id',$goodsIdAry)->update(['is_import'=>0, 'goods_status'=>1, 'product_id'=>$productId])) {
            DB::rollBack();
            $this->errMsg = 'good_id_ary';
            return false;
        }
        DB::commit();
        return true;
    }

    public function updateMasterMatch($idAry) {
        if(!$idAry || !is_array($idAry)) return false;
        return Product::whereIn('id',$idAry)->update(['is_import'=>0, 'product_status'=>1]);
    }

    public function updateSlaveMatch($idAry) {
        if(!$idAry || !is_array($idAry)) return false;
        return Product::whereIn('id',$idAry)->update(['is_import'=>0, 'product_status'=>2]);
    }

    public function updateMatch($idAry,$productId) {
        if(!$idAry || !is_array($idAry)) return false;
        if(!$productId) return false;
        return Goods::whereIn('id',$idAry)->update(['is_import'=>0, 'goods_status'=>1, 'product_id'=>$productId]);
    }

    public function batchSetCategory($idAry,$categoryId) {
        if(!$idAry || !is_array($idAry)) return false;
        if(!$categoryId) return false;
        return Product::whereIn('id',$idAry)->update(['category_id'=>$categoryId]);
    }

    public function batchSetCategoryAndRootCategory($idAry,$categoryId,$rootCategoryId) {
        if(!$idAry || !is_array($idAry)) return false;
        if(!$categoryId) return false;
        if(!$rootCategoryId) return false;
        return Product::whereIn('id',$idAry)->update(['category_id'=>$categoryId,'root_category_id'=>$rootCategoryId]);
    }

    public function getPriceFlagByProductId($productId) {
        if(!$productId) return false;
        $sql = "select orignal_website_id, count(*) num from fb_goods where product_id = '{$productId}' group by orignal_website_id";
        //$goodsList = Goods::where(['product_id'=>$productId])->get();
        $goodsList = DB::select($sql);
        if(!$goodsList) return false;
        if(!$this->websiteObj) {
            $this->websiteObj = new Website;
        }
        $websiteHash = $this->websiteObj->getWebsiteHashInstance();
        $hasDomestic = $hasAbroad = $hasDutyfree = 0;
        foreach($goodsList as $k => $v) {
            if($websiteHash[$v->orignal_website_id]['pay_way'] == 1 || $websiteHash[$v->orignal_website_id]['pay_way'] == 2) {
                $hasDomestic = 1;
            }
            if($websiteHash[$v->orignal_website_id]['pay_way'] == 3 || $websiteHash[$v->orignal_website_id]['pay_way'] == 4) {
                $hasAbroad = 1;
            }
            if($websiteHash[$v->orignal_website_id]['pay_way'] == 5) {
                $hasDutyfree = 1;
            }
        }
        if(count($goodsList) < 2) {
            if(!$hasDutyfree) {
                $price_flag = 1;
            } else {
                $price_flag = 3;
            }
        } else {
            if(!$hasDomestic) {
                $price_flag = 3;
            } else {
                if($hasAbroad || $hasDutyfree) {
                    $price_flag = 3;
                } else {
                    $price_flag = 2;
                }
            }
        }
        return $price_flag;
    }

    public function setPriceFlagByProductId($productId) {
        $price_flag = $this->getPriceFlagByProductId($productId);
        if($price_flag) {
            Product::where(['id'=>$productId])->update(['price_flag'=>$price_flag]);    
        }
        //echo $productId.' => '.$price_flag."\n";
    }

     //查询地区
    public function country()
    {
        return $this->belongsTo( Country::class , 'website_country' , 'id' );
    }

    public function category()
    {
        return $this->belongsTo( Category::class , 'category_id' , 'id' );
    }

    public function goods()
    {
        return $this->belongsTo(Goods::class, 'original_product_id', 'original_goods_id');
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class, 'brand_id', 'id');
    }

    public function website()
    {
        return $this->belongsTo(Website::class, 'orignal_website_id', 'id');
    }

    //查询货币
    public function currency()
    {
        return $this->belongsTo( Currency::class , 'website_currency' , 'id' );
    }


}
