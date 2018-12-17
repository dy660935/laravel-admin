<?php

namespace App\Console\Commands;
use DB;

use Illuminate\Console\Command;
use App\Goods;
use App\Price;
use App\Website;

class BatchSetBestPrice extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'BatchSetBestPrice {--website=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    protected $websiteHash;

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
        $websitehash = $websiteObj->getWebsiteHash();
        $website = ($this->option('website'));
        if(!$website) {
            //$website = 'jd';
            $where = '';
        } else {
            $orignalWebsiteId = $websitehash[$website];
            $where = " and orignal_website_id = '{$orignalWebsiteId}' ";
        }
        $goodsObj = new Goods;
        $sql = "select * from fb_product where is_import = 0 and is_master = 1 {$where} ";
        $productList = DB::select($sql);
        $priceObj = new Price;
        echo microtime()."\n";
        echo count($productList)."\n";
        $count = 0;
        foreach($productList as $k => $v) {
            if($priceObj->setBestPrice($v->id,$goodsObj)) {
                $count++;
            }
            if(!($count%1000)) {
                echo ($count/1000).' '.microtime()."\n";
            }
        }
        echo microtime()."\n";
        echo $count."\n";
    }

    private function _payway4goodsInit() {
        $goodsObj = new Goods;
        $sql = "select * from fb_goods where orignal_website_id = 23";
        $goodsList = DB::select($sql);
        if(!$this->websiteHash) {
            $websiteObj = new Website;
            $this->websiteHash = $websiteObj->getWebsiteAry(['id','website_abbreviation']);
        }
        $priceObj = new Price;
        foreach($goodsList as $k => $v) {
            $prices = $priceObj->getShopPrice((array)$v);
            /*echo $v->market_price."\n";
            echo $v->shop_price."\n";
            echo $v->original_price."\n";
            print_r($prices);*/
            Goods::where(['id'=>$v->id])->update(['shop_price'=>$prices['shop_price']]);
        }
    }

    private function _setCurrencyGenre() {
        $goodsObj = new Goods;
        $sql = "select * from fb_goods where currency_genre = ''";
        $goodsList = DB::select($sql);
        if(!$this->websiteHash) {
            $websiteObj = new Website;
            $this->websiteHash = $websiteObj->getWebsiteAry(['id','website_abbreviation']);
        }
        foreach($goodsList as $k => $v) {
            
            $res = DB::connection('mongodb') -> collection($this->websiteHash[$v->orignal_website_id]['website_abbreviation'])->where('good_id',$v->original_goods_id)->get()->toArray();
            if($res) {
                $add_to_field_index = count($res[0]['add_to_field']) - 1;
                if(isset($res[0]['add_to_field'][$add_to_field_index]['currency'])) {
                    echo $res[0]['add_to_field'][$add_to_field_index]['currency']."\n";
                    Goods::where(['id'=>$v->id])->update(['currency_genre'=>$res[0]['add_to_field'][$add_to_field_index]['currency']]);
                } else {
                    echo $this->websiteHash[$v->orignal_website_id]['website_abbreviation'].':'.$v->original_goods_id.'无'."\n";
                }    
            } else {
                echo 'null'."\n";
            }
        }
    }

    private function _getShopPrice() {
        $goodsObj = new Goods;
        $priceObj = new Price();
        $sql = 'select * from fb_goods where orignal_website_id = 7 and is_import = 1 limit 1000';
        $sql = "select * from fb_goods where id = 987048";
        $goodsList = DB::select($sql);
        foreach($goodsList as $k => $v) {
            if(!isset($v->currency_genre)) {
                echo $v->id."\n";
                continue;
            }
            if(!isset($v->market_price)) {
                echo $v->id."\n";
                break;
            }
            $prices = $priceObj->getShopPrice((array)$v);
            $v = (array)$v;
            if($v['shop_price'] != $prices['shop_price']) {
                if(!$prices['shop_price']) {
                    print_r($v);
                }
                if($v['is_postage'] == 2) {
                    echo $v['postage_price']."\n";
                } else {
                    echo $v['postage_price']."\n";
                }
                if($v['is_cross_border_tax_in'] == 2) {
                    echo "不含税\n";
                    echo $v['currency_genre'].' : market_price : '.$v['market_price'].'=>'.$prices['market_price']."\n";
                    echo $v['currency_genre'].' : shop_price   : '.$v['shop_price'].' + '.$v['cross_border_tax'].' =>'.$prices['shop_price']."\n";
                } else {
                    echo "含税\n";
                    echo $v['currency_genre'].' : market_price : '.$v['market_price'].'=>'.$prices['market_price']."\n";
                    echo $v['currency_genre'].' : shop_price   : '.$v['shop_price'].' + '.$v['cross_border_tax'].' =>'.$prices['shop_price']."\n";
                }
            }
            
            //print_r($prices);
        }
        //echo count($goodsList);
    }
}
