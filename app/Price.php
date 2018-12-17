<?php

namespace App;

class Price extends Base
{
    protected $websiteHash;

    protected $currencyHash;

    protected $a;

    public function setBestPrice( $productId , Goods $goodsObj )
    {
        $goodsObj->setBestPriceByProductId( $productId );
        $goodsList = $goodsObj->getBestPriceByProductId( $productId );
        if( isset( $goodsList[ 0 ] ) && $goodsList[ 0 ] ) {
            return $goodsObj->setBestPrice( $goodsList[ 0 ]->id );
        }
        return false;
    }

    public function getShopPrice( $result )
    {
        //根据网站关键字查出网站上品属于类型
        if( !$this->websiteHash ) {
            $websiteObj = new Website;
            $this->websiteHash = $websiteObj->getWebsiteAry( [ 'id' , 'pay_way' ] );
        }
        if( !$this->currencyHash ) {
            $currencyObj = new Currency();
            $this->currencyHash = $currencyObj->getCurrencyHash();
        }
        $website_type = $this->websiteHash[ $result[ 'orignal_website_id' ] ];

        $price = $prices = [];
        //运费计算
        if( $result[ 'is_postage' ] == 2 ) {        //有运费
            $postage_price = floatval( $result[ 'postage_price' ] );
        } else {                                    //免运费
            $postage_price = 0;
        }

        if( $website_type[ 'pay_way' ] == 1 ) {           //国内直邮
            //商品的划线价格
            $price[ 'market_price' ] = floatval( $result[ 'market_price' ] );
            //最终价格 = 售价 + 运费
            $price[ 'shop_price' ] = floatval( $result[ 'original_price' ] ) + $postage_price;
        } elseif( $website_type[ 'pay_way' ] == 2 ) {     //海外直邮
            //跨境税计算
            if( $result[ 'is_cross_border_tax_in' ] == 2 ) {        //不包税
                $total = floatval( $result[ 'cross_border_tax' ] );
            } else {                                            //包税
                $total = 0;
            }
            var_dump( $total );
            //商品的划线价格
            $price[ 'market_price' ] = floatval( $result[ 'market_price' ] );
            //最终价格 = 售价 + 跨境税 + 运费
            $price[ 'shop_price' ] = floatval( $result[ 'original_price' ] ) + $total + $postage_price;
        } elseif( $website_type[ 'pay_way' ] == 3 ) {     //海淘直邮
            //关税计算
            if( $result[ 'is_import_fee_in' ] == 2 ) {          //不包税
                $price_import = floatval( $result[ 'import_fee' ] );
            } else {                                             //包税
                $price_import = 0;
            }
            //商品的划线价格
            $price[ 'market_price' ] = floatval( $result[ 'market_price' ] );
            //最终价格 = 售价 + 关税 + 运费
            $price[ 'shop_price' ] = floatval( $result[ 'original_price' ] ) + $price_import + $postage_price;
        } elseif( $website_type[ 'pay_way' ] == 4 ) {     //海淘转运
            //关税计算
            if( $result[ 'is_import_fee_in' ] == 2 ) {          //不包税
                $price_import = floatval( $result[ 'import_fee' ] );
            } else {                                            //包税
                $price_import = 0;
            }
            //商品的划线价格
            $price[ 'market_price' ] = floatval( $result[ 'market_price' ] );
            //最终价格 = 售价 + 关税 + 运费
            $price[ 'shop_price' ] = floatval( $result[ 'original_price' ] ) + floatval( $result[ 'original_price' ] ) * 0.095 + $price_import + $postage_price;
        } elseif( $website_type[ 'pay_way' ] == 5 ) {     //免税店
            //当地消费税计算
            if( $result[ 'is_local_tax_in' ] == 2 ) {
                //商品的划线价格
                $price[ 'market_price' ] = floatval( $result[ 'market_price' ] );
                //最终价格 = 免税价/售价 + 运费    
                $price[ 'shop_price' ] = ( $result[ 'duty_free_price' ] > 0 ?
                        floatval( $result[ 'duty_free_price' ] ):
                        $result[ 'original_price' ] ) + $postage_price;
            } else {
                //商品的划线价格
                $price[ 'market_price' ] = floatval( $result[ 'market_price' ] );
                //最终价格 = 退税价 + 运费
                $price[ 'shop_price' ] = floatval( $result[ 'tax_refund_price' ] ) + $postage_price;
            }
        } else {                                        //其他
            //商品的划线价格
            $price[ 'market_price' ] = floatval( $result[ 'market_price' ] );
            //最终价格 = 售价 + 运费
            $price[ 'shop_price' ] = floatval( $result[ 'original_price' ] ) + $postage_price;
        }
        //换算汇率
        if($result[ 'currency_genre' ] == 'CNY') {
            $prices['market_price'] = intval(ceil($price['market_price']));
            $prices['shop_price'] = intval(ceil($price['shop_price']));
        } else {
            if(isset($this->currencyHash[$result['currency_genre']])) {
                $prices['market_price'] = intval(ceil($price['market_price'] * $this->currencyHash[$result['currency_genre']] / 100));
                $prices['shop_price'] = intval(ceil($price['shop_price'] * $this->currencyHash[$result['currency_genre']] / 100));
            } else {
                $prices['market_price'] = intval(ceil($price['market_price']));
                $prices['shop_price'] = intval(ceil($price['shop_price']));
            }
        }
        return $prices;
    }
}