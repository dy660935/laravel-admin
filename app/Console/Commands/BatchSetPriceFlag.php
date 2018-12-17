<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;
use App\Product;
use App\Website;
use App\Goods;

class BatchSetPriceFlag extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'BatchSetPriceFlag';

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
        $sql = "select count(*) countNum from fb_product where product_status = 1";
        $count = DB::select($sql);
        $startPage = 0;
        if(isset($count[0]->countNum) && $count[0]->countNum) {
            $count = $count[0]->countNum;
            $pageSize = 1000;
            $page = ceil($count/$pageSize);
            //test start
            /*$page = 1;
            $pageSize = 1000;*/
            //test end
            $now  = date('Y-m-d H:i:s');
            $goodsObj = new Goods();
            $productObj = new Product;
            $tmpSpecHash = [];
            for($i = $startPage; $i < $page; $i++) {
                $offset = $i * $pageSize;
                $sql = "select id from fb_product where product_status = 1 limit {$offset}, {$pageSize}";
                $goodsList = DB::select($sql);
                $tmp = [];
                foreach($goodsList as $k => $v) {
                    $price_flag = $productObj->getPriceFlagByProductId($v->id);
                    if($price_flag) {
                        $tmp[$price_flag][] = $v->id;    
                    }
                }
                if($tmp) {
                    //print_r($tmp);
                    foreach($tmp as $k => $v) {
                        Product::whereIn('id',$v)->update(['price_flag'=>$k]);
                    }
                }
                echo $offset.' '.microtime()."\n";
            }
        }
    }
}
