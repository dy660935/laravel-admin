<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;
use App\Website;
use App\Product;
use App\Category;

class GoodsProductInit extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'GoodsProductInit';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
    public function handle()
    {
        $websiteObj = new Website();
        $websiteHash = $websiteObj->getWebsiteHash();
        $sql = "select original_goods_id,orignal_website from fb_goods where goods_status = 1";
        $goodsList = DB::select($sql);
        $productObj = new Product;
        $categoryObj = new Category;
        $categoryObj->setCategoryStatus(0);
        $productNum = $hasNum = $loseNum = 0;
        $now  = date('Y-m-d H:i:s');
        if($goodsList) {
            foreach($goodsList as $m => $n) {
                $goodsInfo = DB::connection('mongodb') -> collection($n->orignal_website)->where('good_id',$n->original_goods_id)->get()->toArray();
                //print_r($goodsInfo);
                if($goodsInfo) {
                    $v = $goodsInfo[0];
                    if(!isset($v['good_from'])) {
                        $v['good_from'] = $website;
                    }
                    if(!isset($websiteHash[$v['good_from']])) {
                        continue;
                    }
                    if($v['price_text'] == '[Sponsored]') {
                        continue;
                    }
                    if($v['price_text'] == '[]') {
                        continue;
                    }
                    if(!$v['title']) {
                        continue;
                    }
                    $product = $productObj->getProductInfoByWhere(['original_product_id'=>$v['good_id'],'orignal_website_id'=>$websiteHash[$v['good_from']]]);
                    if(!$product) {
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
                        $productInfo['is_import'] = 0;
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
                        $hasNum++;
                    }
                } else {
                    $loseNum++;
                }
            }
        }
        echo 'goods num '.count($goodsList)."\n";
        echo 'lose num '.$loseNum."\n";
        echo 'has num '.$hasNum."\n";
        echo 'import num '.$productNum."\n";
    }
}
