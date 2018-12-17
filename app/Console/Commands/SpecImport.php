<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Goods;
use App\Website;
use App\Specs;
use DB;
use App\GoodsSpecs;

class SpecImport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'SpecImport {--website=} {--startpage=}';

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
        $startPage = $this->option('startpage');
        if(!$startPage) {
            $startPage = 0;
        }
        $goodsNum = $specNum = 0;
        $goodsList = $importGoodsNum = [];
        $orignalWebsiteId = $websitehash[$website];
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
            $tmpSpecHash = [];
            for($i = $startPage; $i < $page; $i++) {
                $offset = $i * $pageSize;
                $sql = "select id,good_specs from fb_goods where orignal_website_id = '{$orignalWebsiteId}' limit {$offset}, {$pageSize}";
                $goodsList = DB::select($sql);
                if($goodsList) {
                    foreach($goodsList as $k => $v) {
                        if($v->good_specs) {
                            $specsAry = (json_decode($v->good_specs,true)); 
                            foreach($specsAry as $m => $n) {
                                if($n != 'SINGLE' && $n) {
                                    if(mb_strlen($n,'utf-8') > 50) {
                                        continue;
                                    }
                                    $specParam = ['spec_name'=>$n];
                                    if(isset($tmpSpecHash[$n])) {
                                        $specId = $tmpSpecHash[$n];
                                    } else {
                                        $specInfo = Specs::where($specParam)->first(['id']);
                                        if(!$specInfo) {
                                            $specId = Specs::insertGetId($specParam);
                                            $specNum++;
                                        } else {
                                            $specId = $specInfo->id;
                                        }  
                                        $tmpSpecHash[$n] = $specId;  
                                    }
                                    
                                    $goodsSpecParam = ['spec_id'=>$specId,'goods_id'=>$v->id];
                                    $goodsSpecInfo = GoodsSpecs::where($goodsSpecParam)->first(['id']);
                                    if(!$goodsSpecInfo) {
                                        $goodsSpecParam['orignal_website_id'] = $orignalWebsiteId;
                                        GoodsSpecs::insertGetId($goodsSpecParam);
                                        $importGoodsNum[$v->id] = 1;
                                    }
                                }
                            }
                        }
                    }
                }
                echo $offset .' '. microtime()."\n";
            }
        }
        echo 'goods num : '.count($goodsList).' ; importGoodsNum : '.count($importGoodsNum).' ; specNum : '.$specNum;
    }
}
