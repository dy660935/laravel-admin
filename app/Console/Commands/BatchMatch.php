<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;
use App\Product;
use App\Goods;
use App\Website;

class BatchMatch extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'BatchMatch {--website=}';

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

    public function __call($name,$arguments) {
        echo $name.' action not exists';
    }

    public function match($website) {
        $websiteObj = new Website();
        $websiteHash = $websiteObj->getWebsiteHash();
        $orignalWebsiteId = $websiteHash[$website];
        $productObj = new Product;
        $goodsObj = new Goods;
        $sql = "select count(*) countNum, main_code from fb_goods where main_code != '' and orignal_website_id = '{$orignalWebsiteId}' and is_import = 1  group by main_code having count(*) > 1";
        $mainCodeList = DB::select($sql);
        $doMatchCount = 0;
        $allCount = 0;
        if($mainCodeList) {
            foreach($mainCodeList as $k => $v) {
                $allCount += $v->countNum;
                $sql = "select id,product_id,is_import,original_goods_id,original_goods_url,currency_genre,shop_price from fb_goods where orignal_website_id = '{$orignalWebsiteId}' and is_import = 1 and main_code = '{$v->main_code}'";
                $goodsList = DB::select($sql);
                if($goodsList) {
                    $cur_price = $goodsList[0]->shop_price;
                    $cur_product_id = $goodsList[0]->product_id;
                    $cur_goods_id = $goodsList[0]->id;
                    $productIdAry = $goodsIdAry = [];
                    foreach($goodsList as $m => $n) {
                        if($n->shop_price < $cur_price) {
                            $cur_price = $n->shop_price;
                            $cur_product_id = $n->product_id;
                            $cur_goods_id = $n->id;
                        }
                        $productIdAry[$n->product_id] = 1;
                        $goodsIdAry[$n->id] = 1;
                    }
                    unset($productIdAry[$cur_product_id]);
                    $productIdAry = array_keys($productIdAry);
                    $goodsIdAry = array_keys($goodsIdAry);
                    if($productObj->doMatch($productIdAry,$cur_product_id,$goodsIdAry)) {
                        $doMatchCount++;
                        $goodsObj->setBestPrice($cur_goods_id);
                        $productObj->setPriceFlagByProductId($cur_product_id);
                        echo $productObj->errMsg;
                    }
                }
            }
        }
        echo '总记录：'.($allCount).' 合并为：'.count($mainCodeList).' 合并成功：'.$doMatchCount;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $website = ($this->option('website'));
        if(!$website) {
            exit('website empty');
        }
        //$actionName = $website.'Match';
        $this->match($website);
        exit;
        
    }
}
