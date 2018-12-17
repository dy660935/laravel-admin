<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Website;
use App\Goods;
use App\Price;
use DB;

class BatchSetPrice extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'BatchSetPrice {--website=}';

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
        $websitehash = $websiteObj->getWebsiteHash();
        $website = ($this->option('website'));
        if(!$website) {
            $website = 'jd';
        }
        $orignalWebsiteId = $websitehash[$website];
        $startPage = 0;
        $priceObj = new Price;
        $goodsNum = $specNum = 0;
        $goodsList = $importGoodsNum = [];
        $sql = "select count(*) countNum from fb_goods where orignal_website_id = '{$orignalWebsiteId}'";
        $count = DB::select($sql);
        if(isset($count[0]->countNum) && $count[0]->countNum) {
            $count = $count[0]->countNum;
            $pageSize = 1000;
            $page = ceil($count/$pageSize);
            //test start
            /*$page = 1;
            $pageSize = 10;*/
            //test end
            $now  = date('Y-m-d H:i:s');
            $goodsObj = new Goods();

            for($i = $startPage; $i < $page; $i++) {
                $offset = $i * $pageSize;
                $sql = "select * from fb_goods where orignal_website_id = '{$orignalWebsiteId}' limit {$offset}, {$pageSize}";
                $goodsList = DB::select($sql);
                if($goodsList) {
                    foreach($goodsList as $k => $v) {
                        $prices = $priceObj->getShopPrice((array)$v);
                        Goods::where(['id'=>$v->id])->update(['shop_price'=>$prices['shop_price']]);
                    }
                }
                echo microtime()."\n";
            }
        }
        echo 'goods num : '.count($goodsList).' ; importGoodsNum : '.count($importGoodsNum).' ; specNum : '.$specNum;
    }
}
