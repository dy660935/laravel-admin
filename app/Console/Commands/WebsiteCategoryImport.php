<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;
use App\WebsiteCategory;
use App\Website;

class WebsiteCategoryImport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'WebsiteCategoryImport {--website=}';

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
    private function saveWebsiteCategory($categoryTree) {
        $categoryObj = new WebsiteCategory();
        if(!$categoryTree || !is_array($categoryTree)) return false;
        foreach($categoryTree as $k => $v) {
            if(!$categoryObj->checkExistsByWhere(['category_id'=>$v['category_id'],'orignal_website_id'=>$v['orignal_website_id']])) {
                WebsiteCategory::insert($v);
            }
        }
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
        $website = ($this->option('website'));
        if(!$website) {
            $website = 'jd';
        }
        //$res = DB::connection('mongodb') -> collection('test')->skip(0)->take(1)->get()->toArray();
        //$res = DB::connection('mongodb') -> collection('test')->take(1)->get()->toArray();
        //$res = DB::connection('mongodb') -> collection('kaola')->where('comment_count','<',10)->orderBy('comment_count','desc')->skip(0)->take(1)->get()->toArray();
        //$res = DB::connection('mongodb') -> collection('kaola')->where('comment_count','>=',1000)->count();
        $startMicro = explode(' ',microtime());
        $start = $startMicro[1] + $startMicro[0];
        //echo microtime()."\n";
        //$res = DB::connection('mongodb') -> collection($website)->where('comment_count','>=',1000)->take(10)->get()->toArray();
        $count = DB::connection('mongodb') -> collection($website)->count();
        $pageSize = 1000;
        $page = ceil($count/$pageSize);
        $productNum = $goodsNum = 0;
        $now  = date('Y-m-d H:i:s');
        $spuNum = $skuNum = 0;
        //$startPage = 6;
        $noCategoryWebsite = [10,11,12,13];
        $startPage = 0;
        //$page = 1;
        $tmp = [];
        for($i = $startPage; $i < $page; $i++) {
            //echo ($i * $pageSize).'----'.$pageSize."\n";
            //$res = DB::connection('mongodb') -> collection($website)->take(100)->get()->toArray();
            $res = DB::connection('mongodb') -> collection($website)->skip($i * $pageSize)->take($pageSize)->get()->toArray();
            $productList = $goodsList = $categoryTree = $productInfo = $goodsInfo = [];
            
            foreach($res as $k => $v) {
                if(!isset($v['good_from'])) {
                    $v['good_from'] = $website;
                }
                /*if(!isset($websiteHash[$v['good_from']])) {
                    continue;
                }
                if(!in_array($websiteHash[$v['good_from']],$noCategoryWebsite)) {*/
                    $categoryInfo = [];
                    $categoryTree = [];
                    $categoryInfo['category_id'] = isset($v['root_category_id']) ? $v['root_category_id'] : 0;
                    $categoryInfo['category_name'] = isset($v['root_category_name']) ? $v['root_category_name'] : '';
                    $categoryInfo['updated_at'] = $now;
                    $categoryInfo['created_at'] = $now;
                    $categoryInfo['orignal_website_id'] = $websiteHash[$v['good_from']];
                    if($categoryInfo['category_id'] && $categoryInfo['category_name']) {
                        $categoryTree[] = $categoryInfo; 
                        if(!isset($tmp[$categoryInfo['category_id']])) {
                            WebsiteCategory::insert($categoryInfo);
                            $tmp[$categoryInfo['category_id']] = 1;
                        }
                    }
                    $categoryInfo = [];
                    $categoryInfo['category_id'] = isset($v['category_id']) ? $v['category_id'] : 0;
                    $categoryInfo['parent_category_id'] = $v['root_category_id'];
                    $categoryInfo['category_name'] = isset($v['category_name']) ? $v['category_name'] : '';
                    $categoryInfo['category_level'] = 2;
                    $categoryInfo['updated_at'] = $now;
                    $categoryInfo['created_at'] = $now;
                    $categoryInfo['orignal_website_id'] = $websiteHash[$v['good_from']];
                    if($categoryInfo['category_id'] && $categoryInfo['category_name']) {
                        $categoryTree[] = $categoryInfo;  
                        if(!isset($tmp[$categoryInfo['category_id']])) {
                            WebsiteCategory::insert($categoryInfo);
                            $tmp[$categoryInfo['category_id']] = 1;
                        }
                    }
                    $categoryInfo = [];
                    $categoryInfo['category_id'] = isset($v['sub_category_id']) ? $v['sub_category_id'] : 0;
                    $categoryInfo['parent_category_id'] = $v['category_id'];
                    $categoryInfo['category_name'] = isset($v['sub_category_name']) ? $v['sub_category_name'] : '';
                    $categoryInfo['category_level'] = 3;
                    $categoryInfo['updated_at'] = $now;
                    $categoryInfo['created_at'] = $now;
                    $categoryInfo['orignal_website_id'] = $websiteHash[$v['good_from']];
                    if($categoryInfo['category_id'] && $categoryInfo['category_name']) {
                        $categoryTree[] = $categoryInfo;    
                        if(!isset($tmp[$categoryInfo['category_id']])) {
                            WebsiteCategory::insert($categoryInfo);
                            $tmp[$categoryInfo['category_id']] = 1;
                        }
                    }
                    //print_r($categoryTree);
                    //print_r($tmp);
                    //exit;
                    
                    //$this->saveWebsiteCategory($categoryTree);
                //}
            }
            $midMicro = explode(' ',microtime());
            $mid = $midMicro[1] + $midMicro[0];
            echo ($pageSize * $i).' '.$mid."\n";
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
