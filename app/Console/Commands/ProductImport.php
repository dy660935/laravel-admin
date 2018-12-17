<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;
use App\WebsiteCategory;
use App\Category;
use App\Product;
use App\Goods;
use App\Website;
use App\Shop;
use App\GoodsMapping;
use App\Price;

class ProductImport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ProductImport {--website=} {--startpage=}';

    protected $shopObj;

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    private $_brandIdPassAry = [74,49,71,66,39,29,69,31,1,46,15,7,38,93,48,80,67,42,58,10,13,76,5,35,41,54,59,61,70,77,78,85,9,11,14,19,33,55,62,65,73,91,97,64,72];

    private $_jxkExceptCountry = ['墨西哥','巴西','摩洛哥','智利','秘鲁','芬兰','克罗地亚','爱尔兰','波兰','丹麦','瑞典','卢森堡','斯洛伐克','奥地利','葡萄牙','特立尼达和多巴哥共和国','摩纳哥','挪威','印度','菲律宾','印度尼西亚','捷克','匈牙利'];
    private $_customDeliveryFrom = [
        'kaola' => '网易考拉',
        'tmallhk' => '天猫国际',
        'jd_global' => '京东国际',
        'vip_global' => '唯品国际',
    ];
    private $_customTaxFreeZone = [
        'vip_global' => '自营保税仓',
    ];
    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */

    private function saveAttr($data) {
        if(!$data || !is_array($data)) return [];
        $attrList = [];
        foreach($data as $k => $v) {
            if($k == '功效' or $k == '商品名称') continue;
            if(mb_strlen($v) > 45) continue;
            $attribute = DB::table('fb_attribute_name_enum')->where('attribute_name',$k)->first(['id']);
            if($attribute){
                $attribute = get_object_vars($attribute);
                $attribute_name_id = $attribute['id'];
            }else{
                $attribute_name_id = DB::table('fb_attribute_name_enum')->insertGetId([
                    'attribute_name'=>$k,
                    'created_at'=>date('Y-m-d H:i:s',time()),
                ]);
            }
            $attribute_value = DB::table('fb_attribute_value_enum')->where('attribute_value',$v)->first(['id']);
            if (!$attribute_value){
                $attribute_value_id=DB::table('fb_attribute_value_enum')->insertGetId([
                    'attribute_value'=>$v,
                    'attribute_id'=>$attribute_name_id,
                    'created_at'=>date('Y-m-d H:i:s',time()),
                ]);
            }else{
                $attribute_value = get_object_vars($attribute_value);
                $attribute_value_id = $attribute_value['id'];
            }
            $attrList[] = ['attribute_id'=>$attribute_name_id,'attribute_value_id'=>$attribute_value_id];
        }
        return $attrList;
    }

    private function saveGoodsAttr($attr,$goodsId,$orignalGoodsId) {
        if(!$attr || !is_array($attr) || !$goodsId || !$orignalGoodsId) return false;
        $now = date('Y-m-d H:i:s');
        foreach($attr as $k => $v) {
            $v['goods_id'] = $goodsId;
            $v['original_goods_id'] = $orignalGoodsId;
            $v['created_at'] = $now;
            GoodsMapping::insert($v);
        }
    }

    private function saveShop($shopInfo) {
        if(!$this->shopObj) {
            $this->shopObj = new Shop();
        }
        if(!$shopInfo || !is_array($shopInfo)) return false;
        $shop = $this->shopObj->getShopInfoByWhere(['shop_name'=>$shopInfo['shop_name'],'orignal_website_id'=>$shopInfo['orignal_website_id']]);
        if($shop) {
            return $shop->id;
        } else {
            return Shop::insertGetId($shopInfo);
        }
        return false;
    }

    private function _fieldAdd($v,$website) {
        if($website == 'macys') {
            $v['is_oversea'] = 1;
        }
        if($website == 'lookfantastic') {
            $v['is_oversea'] = 1;
        }
        if($website == 'american') {
            $v['is_oversea'] = 1;
        }
        if($website == 'net_a_porter') {
            $v['is_oversea'] = 1;
        }
        if($website == 'farfetch') {
            $v['is_oversea'] = 1;
        }
        if($website == 'jxk') {
            $v['is_oversea'] = 1;
        }
        if($website == 'feelunique') {
            $v['is_oversea'] = 1;
        }
        if($website == 'optimism_mail') {
            $v['is_oversea'] = 1;
        }
        return $v;
    }

    private function _getExcuteTime($start, $end) {
        return round($end - $start,3);
    }

    public function handle()
    {
        $websiteObj = new Website();
        $websiteHash = $websiteObj->getWebsiteHash();
        $websiteAry = $websiteObj->getWebsiteAry([ 'id', 'update_type' ]);
        $website = ($this->option('website'));
        if(!$website) {
            $website = 'jd';
        }
        $startPage = $this->option('startpage');
        if(!$startPage) {
            $startPage = 0;
        }
        //$res = DB::connection('mongodb') -> collection('test')->skip(0)->take(1)->get()->toArray();
        //$res = DB::connection('mongodb') -> collection('test')->take(1)->get()->toArray();
        //$res = DB::connection('mongodb') -> collection('kaola')->where('comment_count','<',10)->orderBy('comment_count','desc')->skip(0)->take(1)->get()->toArray();
        //$res = DB::connection('mongodb') -> collection('kaola')->where('comment_count','>=',1000)->count();
        $startMicro = explode(' ',microtime());
        $start = $startMicro[1] + $startMicro[0];
        //echo microtime()."\n";
        //$res = DB::connection('mongodb') -> collection($website)->where('comment_count','>=',1000)->take(10)->get()->toArray();
        if($website == 'jxk') {
            $count = DB::connection('mongodb') -> collection($website)->whereIn('brandID',$this->_brandIdPassAry)->whereNotIn('country',$this->_jxkExceptCountry)->where('is_onsell',1)->count();
        } else {
            $count = DB::connection('mongodb') -> collection($website)->whereIn('brandID',$this->_brandIdPassAry)->where('is_onsell',1)->count();
        }
        $pageSize = 1000;
        $page = ceil($count/$pageSize);
        $productNum = $goodsNum = 0;
        $now  = date('Y-m-d H:i:s');
        $productObj = new Product();
        $goodsObj = new Goods();
        $categoryObj = new Category();
        $priceObj = new Price();
        $categoryObj->setCategoryStatus(0);
        $spuNum = $skuNum = 0;
        //$startPage = 8;
        $noCategoryWebsite = [10,11,12,13];
        //$startPage = 0;
        //$page = 1;
        $dbName = $website;
        //$page = 1;
        for($i = $startPage; $i < $page; $i++) {
            //echo ($i * $pageSize).'----'.$pageSize."\n";
            //$res = DB::connection('mongodb') -> collection($website)->take(100)->get()->toArray();
            if($website == 'jxk') {
                $res = DB::connection('mongodb') -> collection($website)->whereIn('brandID',$this->_brandIdPassAry)->whereNotIn('country',$this->_jxkExceptCountry)->where('is_onsell',1)->skip($i * $pageSize)->take($pageSize)->get()->toArray();
            } else {
                $res = DB::connection('mongodb') -> collection($website)->whereIn('brandID',$this->_brandIdPassAry)->where('is_onsell',1)->skip($i * $pageSize)->take($pageSize)->get()->toArray();
            }
            $errorNum = $fromErrorNum = $titleErrorNum = 0;
            $productList = $goodsList = $categoryTree = $productInfo = $goodsInfo = [];
            foreach($res as $k => $v) {
                //if(in_array($v['brandID'],$this->_brandIdPassAry)) {
                    if(!isset($v['good_from'])) {
                        $v['good_from'] = $website;
                    }
                    if(!isset($websiteHash[$v['good_from']])) {
                        $fromErrorNum++;
                        continue;
                    }
                    if(isset($v['price_text']) && $v['price_text'] == '[Sponsored]') {
                        $errorNum++;
                        continue;
                    }
                    if(isset($v['price_text']) && $v['price_text'] == '[]') {
                        $errorNum++;
                        continue;
                    }
                    if(!$v['title']) {
                        $titleErrorNum++;
                        continue;
                    }
                    $add_to_field_index = count($v['add_to_field']) - 1;
                    if(isset($v['add_to_field'][$add_to_field_index]['price_text']) && $v['add_to_field'][$add_to_field_index]['price_text'] == '[]') {
                        $errorNum++;
                        continue;
                    }
                    $v = $this->_fieldAdd($v,$website);
                    $product = $productObj->getProductInfoByWhere(['original_product_id'=>$v['good_id'],'orignal_website_id'=>$websiteHash[$v['good_from']]]);
                    if(!$product) {
                        if(isset($v['origin_production']) && $v['origin_production']) {
                            $origin_production = 0;
                            if(isset($v['base_info']) && $v['base_info']) {
                                foreach($v['base_info'] as $kk => $vv) {
                                    if(preg_match('/产地/',$kk)) {
                                        $origin_production = 1;
                                        break;
                                    }
                                }
                                if(!$origin_production) {
                                    $v['base_info']['产地'] = $v['origin_production'];
                                }
                            }
                        }
                        $productInfo['orignal_website_id'] = isset($websiteHash[$v['good_from']]) ? $websiteHash[$v['good_from']] : 0;
                        $productInfo['category_id'] = isset($v['sub_category_id']) ? $v['sub_category_id'] : 0;
                        if($productInfo['orignal_website_id'] != 7) {
                            if(isset($v['sub_category_name']) && $v['sub_category_name']) {
                                $categoryName = $v['sub_category_name'];
                                if(!isset($categoryNameTmpAry[$categoryName])) {
                                    $categoryId = $categoryObj->getThirdCategoryIdByCategoryName($categoryName);
                                    $categoryNameTmpAry[$categoryName] = $categoryId;
                                } else {
                                    $categoryId = $categoryNameTmpAry[$categoryName];
                                }
                                $productInfo['category_id'] = $categoryId;
                            }
                        }
                        $productInfo['root_category_id'] = $categoryObj->getRootCategoryIdByThridCategoryId($productInfo['category_id']);
                        $productInfo['brand_id'] = $v['brandID'];
                        $productInfo['product_sn'] = $v['good_id'];
                        $productInfo['original_product_id'] = $v['good_id'];
                        if(isset($v['title_ch']) && $v['title_ch']) {
                            $productInfo['product_name'] = $v['title_ch'];    
                        } else {
                            $productInfo['product_name'] = $v['title'];
                        }
                        $add_to_field_index = count($v['add_to_field']) - 1;
                        $productInfo['currency_genre'] = isset($v['add_to_field'][$add_to_field_index]['currency']) ? $v['add_to_field'][$add_to_field_index]['currency'] : 'CNY';
                        $productInfo['product_describle'] = $v['title'];
                        if(isset($v['comment_count']) && $v['comment_count'] >= 1000) {
                            $productInfo['product_status'] = 2;
                        } else {
                            $productInfo['product_status'] = 3;
                        }
                        $productInfo['is_import'] = 1;
                        $productInfo['updated_at'] = $now;
                        $productInfo['created_at'] = $now;
                        if(isset($v['good_image_path']) && $v['good_image_path']) {
                            $productInfo['product_image'] = $v['good_image_path'];
                        } else {
                            if(isset($v['images']) && $v['images']) {
                                $productInfo['product_image'] = $v['images'];
                            }
                            if(isset($v['good_img']) && $v['good_img']) {
                                $productInfo['product_image'] = $v['good_img'];
                            } 
                            if(isset($v['iamges']) && $v['iamges']) {
                                $productInfo['product_image'] = $v['iamges'];
                            }    
                        }
                        $productInfo['product_label'] = [];
                        if(isset($v['root_category_name']) && $v['root_category_name']){
                            $productInfo['product_label'][] = $v['root_category_name'];
                        }
                        if(isset($v['category_name']) && $v['category_name']){
                            $productInfo['product_label'][] = $v['category_name'];
                        }
                        if(isset($v['sub_category_name']) && $v['sub_category_name']){
                            $productInfo['product_label'][] = $v['sub_category_name'];
                        }
                        if($productInfo['product_label']) {

                        }
                        $productInfo['product_label'] = implode(',',$productInfo['product_label']);
                        $product_id = DB::table('fb_product')->insertGetId($productInfo);
                        if($product_id) {
                            $productNum++;
                        }
                    } else {
                        $product_id = $product['id'];
                    }
                    if(!$goodsObj->checkExistsByWhere(['original_goods_id'=>$v['good_id'],'orignal_website'=>$v['good_from']])) {
                        if($product_id) {
                            $goodsInfo['product_id'] = $product_id;
                            $goodsInfo['orignal_website'] = $v['good_from'];
                            $goodsInfo['orignal_website_id'] = isset($websiteHash[$v['good_from']]) ? $websiteHash[$v['good_from']] : 0;
                            $goodsInfo['original_goods_url'] = $v['good_url'] ? $v['good_url']: '';
                            $goodsInfo['original_goods_id'] = $v['good_id'];
                            
                            $goodsInfo['market_price'] = isset($v['add_to_field'][$add_to_field_index]['original_cost']) ? $v['add_to_field'][$add_to_field_index]['original_cost'] : 0;
                            if(!$goodsInfo['market_price']) {
                                $goodsInfo['market_price'] = isset($v['original_cost']) ? $v['original_cost'] : 0;
                            }
                            $goodsInfo['shop_price'] = $v['add_to_field'][$add_to_field_index]['price_text'];
                            if($goodsInfo['shop_price'] == '[Sponsored]') {
                                $goodsInfo['shop_price'] = isset($v['price_text']) ? $v['price_text'] : $v['add_to_field'][$add_to_field_index]['price_text'];
                            }
                            if($goodsInfo['market_price'] == '[Sponsored]') {
                                $goodsInfo['market_price'] = $goodsInfo['shop_price'];
                            }
                            if(strpos($goodsInfo['market_price'],'-') !== false) {
                                $marketPriceAry = explode('-',$goodsInfo['market_price']);
                                $goodsInfo['market_price'] = $marketPriceAry[0];
                            }
                            $goodsInfo['shop_price'] = str_replace(',','',$goodsInfo['shop_price']);
                            $goodsInfo['market_price'] = str_replace(',','',$goodsInfo['market_price']);
                            $goodsInfo['original_price'] = $goodsInfo['shop_price'];
                            if(isset($v['add_to_field'][$add_to_field_index]['cross_border_tax']) && $v['add_to_field'][$add_to_field_index]['cross_border_tax']) {
                                $goodsInfo['cross_border_tax'] =  $v['add_to_field'][$add_to_field_index]['cross_border_tax'] > 0 ? $v['add_to_field'][$add_to_field_index]['cross_border_tax'] : 0 ;
                            } else {
                                $goodsInfo['cross_border_tax'] = 0;
                            }
                            $goodsInfo['is_cross_border_tax_in'] = isset($v['add_to_field'][$add_to_field_index]['is_cross_border_tax_in']) ? ($v['add_to_field'][$add_to_field_index]['is_cross_border_tax_in'] > 0 ? $v['add_to_field'][$add_to_field_index]['is_cross_border_tax_in'] : 2) : 2;
                            $goodsInfo['price_updated_at'] = date('Y-m-d H:i:s',$v['add_to_field'][$add_to_field_index]['crawl_date']);
                            $goodsInfo['is_postage'] = $v['add_to_field'][$add_to_field_index]['is_postage'];
                            $goodsInfo['postage_price'] = $v['add_to_field'][$add_to_field_index]['postage_price'] > 0 ? $v['add_to_field'][$add_to_field_index]['postage_price'] : 0;
                            $goodsInfo['stock_number'] = isset($v['quantity']) ? ($v['quantity'] > 0 ? $v['quantity'] : 0) : 100;
                            if(isset($v['comment_count']) && $v['comment_count'] >= 1000) {
                                $goodsInfo['goods_status'] = 2;
                            } else {
                                $goodsInfo['goods_status'] = 3;
                            }
                            $goodsInfo['is_import'] = 1;
                            $goodsInfo['updated_at'] = $now;
                            $goodsInfo['created_at'] = $now;
                            $goodsInfo['currency_genre'] = isset($v['add_to_field'][$add_to_field_index]['currency']) ? $v['add_to_field'][$add_to_field_index]['currency'] : 'CNY';
                            if(isset($v['title_ch']) && $v['title_ch']) {
                                $goodsInfo['goods_name'] = $v['title_ch'];
                                $goodsInfo['goods_original_name'] = $v['title'];    
                            } else {
                                $goodsInfo['goods_name'] = $v['title'];
                                $goodsInfo['goods_original_name'] = $v['title'];    
                            }
                            $goodsInfo['is_oversea'] = $v['is_oversea'];
                            $goodsInfo['is_self_support'] = isset($v['is_self_support']) ? $v['is_self_support'] : 2;
                            $goodsInfo['comment_count'] = isset($v['comment_count']) ? ($v['comment_count'] > 0 ? $v['comment_count'] : 0) : 0 ;
                            if(isset($v['good_image_path']) && $v['good_image_path']) {
                                $goodsInfo['goods_image'] = $v['good_image_path'];
                            } else {
                                if(isset($v['images']) && $v['images']) {
                                    $goodsInfo['goods_image'] = $v['images'];
                                }
                                if(isset($v['good_img']) && $v['good_img']) {
                                    $goodsInfo['goods_image'] = $v['good_img'];
                                }
                                if(isset($v['iamges']) && $v['iamges']) {
                                    $goodsInfo['goods_image'] = $v['iamges'];
                                }        
                            }
                            //$goodsInfo['base_info'] = isset($v['base_info']) ? json_encode($v['base_info']) : '';
                            $goodsInfo['promotion_info'] = [];
                            if(isset($v['price_tags']) && $v['price_tags']) {
                                if(is_array($v['price_tags'])) {
                                    $goodsInfo['promotion_info']['price_tags'] = $v['price_tags'];    
                                } else {
                                    $goodsInfo['promotion_info']['price_tags'] = [$v['price_tags']];
                                }
                            }
                            if(isset($v['promotion_contents']) && $v['promotion_contents']) {
                                $goodsInfo['promotion_info']['promotion_contents'] = $v['promotion_contents'];
                            }
                            if($goodsInfo['promotion_info']) {
                                $goodsInfo['promotion_info'] = json_encode($goodsInfo['promotion_info']);   
                            } else {
                                $goodsInfo['promotion_info'] = '';
                            }
                            if(isset($v['good_specs']) && $v['good_specs']) {
                                $goodsInfo['good_specs'] = json_encode($v['good_specs']);
                            } else {
                                $goodsInfo['good_specs'] = '';
                            }
                            if(isset($this->_customTaxFreeZone[$website])) {
                                $goodsInfo['tax_free_zone'] = $this->_customTaxFreeZone[$website];
                            } else {
                                $goodsInfo['tax_free_zone'] = isset($v['taxFreeZone']) ? ($v['taxFreeZone'] == '-1' ? '' : $v['taxFreeZone']) : '';
                                if(!$goodsInfo['tax_free_zone']) {
                                    $goodsInfo['tax_free_zone'] = isset($v['tax_free_zone']) ? ($v['tax_free_zone'] == '-1' ? '' : $v['tax_free_zone']) : '';
                                }
                            }
                            $goodsInfo['is_import_fee_in'] = isset($v['add_to_field'][$add_to_field_index]['is_import_fee_in']) ? ($v['add_to_field'][$add_to_field_index]['is_import_fee_in'] > 0 ? $v['add_to_field'][$add_to_field_index]['is_import_fee_in'] : 2) : 2;
                            $goodsInfo['import_fee'] = isset($v['add_to_field'][$add_to_field_index]['import_fee']) ? ($v['add_to_field'][$add_to_field_index]['import_fee'] > 0 ? $v['add_to_field'][$add_to_field_index]['import_fee'] : 0) : 0;
                            $goodsInfo['vip_price'] = isset($v['add_to_field'][$add_to_field_index]['vip_price']) ? ($v['add_to_field'][$add_to_field_index]['vip_price'] > 0 ? $v['add_to_field'][$add_to_field_index]['vip_price'] : 0) : 0;
                            $shopInfo = [];
                            if(isset($v['shop_name']) && $v['shop_name']) {
                                $shopInfo['shop_name'] = $v['shop_name'];
                            }
                            if(!isset($shopInfo['shop_name'])) {
                                if(isset($v['good_shop_name']) && $v['good_shop_name']) {
                                    $shopInfo['shop_name'] = $v['good_shop_name'];
                                }   
                            }
                            if(isset($v['shop_id']) && $v['shop_id']) {
                                $shopInfo['shop_id'] = $v['shop_id'];
                                //$goodsInfo['shop_id'] = $v['shop_id'];
                            }
                            if(isset($v['shop_url']) && $v['shop_url']) {
                                $shopInfo['shop_url'] = $v['shop_url'];
                            }
                            if($shopInfo) {
                                $shopInfo['orignal_website_id'] = isset($websiteHash[$v['good_from']]) ? $websiteHash[$v['good_from']] : 0;
                                if(isset($shopInfo['shop_name'])) {
                                    if($website == 'jxk') {
                                        $shopInfo['shop_thumbnail'] = 'http://six.qqzdj.com.cn/uploadImages/181128/dutyfree-red.png';
                                    }
                                    $shop_id = $this->saveShop($shopInfo);
                                    $goodsInfo['shop_id'] = $shop_id;
                                }
                            }
                            $goodsInfo['transport_city'] = isset($v['transport_city']) ? $v['transport_city'] : '';
                            $goodsInfo['is_onsell'] = isset($v['is_onsell']) ? $v['is_onsell'] : 1;
                            $goodsInfo['is_local_tax_in'] = isset($v['add_to_field'][$add_to_field_index]['is_local_tax_in']) ? $v['add_to_field'][$add_to_field_index]['is_local_tax_in'] : 2;
                            $goodsInfo['local_tax_in_price'] = isset($v['add_to_field'][$add_to_field_index]['local_tax_in_price']) ? ($v['add_to_field'][$add_to_field_index]['local_tax_in_price'] > 0 ? $v['add_to_field'][$add_to_field_index]['local_tax_in_price'] : 0 ) : 0;
                            $goodsInfo['duty_free_price'] = isset($v['add_to_field'][$add_to_field_index]['duty_free_price']) ? ($v['add_to_field'][$add_to_field_index]['duty_free_price'] > 0 ? $v['add_to_field'][$add_to_field_index]['duty_free_price'] : 0 ) : 0;
                            $goodsInfo['tax_refund_price'] = isset($v['add_to_field'][$add_to_field_index]['tax_refund_price']) ? ($v['add_to_field'][$add_to_field_index]['tax_refund_price'] > 0 ? $v['add_to_field'][$add_to_field_index]['tax_refund_price'] : 0 ) : 0;
                            $goodsInfo['has_stock'] = isset($v['has_stock']) ? $v['has_stock'] : 1;
                            $goodsInfo['main_code'] = isset($v['main_code']) ? $v['main_code'] : '';
                            if(isset($this->_customDeliveryFrom[$website])) {
                                $goodsInfo['delivery_from'] = $this->_customDeliveryFrom[$website];
                            } else {
                                $goodsInfo['delivery_from'] = isset($v['delivery_from']) ? ($v['delivery_from'] == '-1' ? '' : $v['delivery_from']) : '';
                            }
                            $goodsInfo['sell_count'] = isset($v['sell_count']) ? $v['sell_count'] : 0;
                            $goodsInfo['original_category_name'] = isset($v['sub_category_name']) ? $v['sub_category_name'] : '';
                            $prices = $priceObj->getShopPrice($goodsInfo);
                            $goodsInfo['shop_price'] = $prices['shop_price'];
                            $goodsInfo['update_type'] = isset($websiteAry[$goodsInfo['orignal_website_id']]['update_type']) ? $websiteAry[$goodsInfo['orignal_website_id']]['update_type'] : 1;
                            $goods_id = DB::table('fb_goods')->insertGetId($goodsInfo);
                            if($goods_id) {
                                /*if(isset($v['base_info']) && $v['base_info']) {
                                    $attr = $this->saveAttr($v['base_info']);
                                    $this->saveGoodsAttr($attr,$goods_id,$v['good_id']);
                                }*/
                                $goodsNum++;
                            }
                        }
                    }
                //}
            }
            $midMicro = explode(' ',microtime());
            $mid = $midMicro[1] + $midMicro[0];
            echo ($pageSize * $i).' '.$mid.' '.$this->_getExcuteTime($start,$mid)."s errorNum:{$errorNum} fromErrorNum:{$fromErrorNum} titleErrorNum:{$titleErrorNum} \n";
        }
        $endMicro = explode(' ',microtime());
        $end = $endMicro[1] + $endMicro[0];
        echo 'product import '.$productNum.' ; goods import '.$goodsNum.' .';
        echo 'execute '.number_format($end - $start,2).' s';
        //print_r($res);
        //$res = DB::connection('mongodb') -> collection('test')->get()->toArray();
        //var_dump($res);
        /*$res = DB::connection('mysql') -> table('fb_website')->get()->toArray();
        var_dump($res);*/
    }
}
