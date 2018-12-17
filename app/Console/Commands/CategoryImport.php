<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;
use App\Category;
use App\Website;

class CategoryImport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'CategoryImport {--website=}';

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

    private function saveCategory($categoryTree) {
        $categoryObj = new Category();
        if(!$categoryTree || !is_array($categoryTree)) return false;
        foreach($categoryTree as $k => $v) {
            if(!$categoryObj->checkExistsByWhere(['category_id'=>$v['category_id'],'orignal_website_id'=>$v['orignal_website_id']])) {
                Category::insert($v);
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
        $websitehash = $websiteObj->getWebsiteHash();
        $website = ($this->option('website'));
        if(!$website) {
            $website = 'jd';
        }
        $startMicro = explode(' ',microtime());
        $start = $startMicro[1] + $startMicro[0];
        $count = DB::connection('mongodb') -> collection($website)->count();
        $pageSize = 1000;
        $page = ceil($count/$pageSize);
        $productNum = $goodsNum = 0;
        $now  = date('Y-m-d H:i:s');
        $spuNum = $skuNum = 0;
        $noCategoryWebsite = [10,11,12,13];
        $startPage = 0;
        $tmp = [];
        for($i = $startPage; $i < $page; $i++) {
            $res = DB::connection('mongodb') -> collection($website)->skip($i * $pageSize)->take($pageSize)->get()->toArray();
            $productList = $goodsList = $categoryTree = $productInfo = $goodsInfo = [];
            
            foreach($res as $k => $v) {
                if(!isset($v['good_from'])) {
                    $v['good_from'] = $website;
                }
                $categoryInfo = [];
                $categoryTree = [];
                $categoryInfo['id'] = isset($v['root_category_id']) ? $v['root_category_id'] : 0;
                $categoryInfo['category_name'] = isset($v['root_category_name']) ? $v['root_category_name'] : '';
                $categoryInfo['updated_at'] = $now;
                $categoryInfo['created_at'] = $now;
                if($categoryInfo['id'] && $categoryInfo['category_name']) {
                    $categoryTree[] = $categoryInfo; 
                    if(!isset($tmp[$categoryInfo['id']])) {
                        Category::insert($categoryInfo);
                        $tmp[$categoryInfo['id']] = 1;
                    }
                }
                $categoryInfo = [];
                $categoryInfo['id'] = isset($v['category_id']) ? $v['category_id'] : 0;
                $categoryInfo['parent_id'] = $v['root_category_id'];
                $categoryInfo['category_name'] = isset($v['category_name']) ? $v['category_name'] : '';
                $categoryInfo['category_level'] = 2;
                $categoryInfo['updated_at'] = $now;
                $categoryInfo['created_at'] = $now;
                if($categoryInfo['id'] && $categoryInfo['category_name']) {
                    $categoryTree[] = $categoryInfo;  
                    if(!isset($tmp[$categoryInfo['id']])) {
                        Category::insert($categoryInfo);
                        $tmp[$categoryInfo['id']] = 1;
                    }
                }
                $categoryInfo = [];
                $categoryInfo['id'] = isset($v['sub_category_id']) ? $v['sub_category_id'] : 0;
                $categoryInfo['parent_id'] = $v['category_id'];
                $categoryInfo['category_name'] = isset($v['sub_category_name']) ? $v['sub_category_name'] : '';
                $categoryInfo['category_level'] = 3;
                $categoryInfo['updated_at'] = $now;
                $categoryInfo['created_at'] = $now;
                if($categoryInfo['id'] && $categoryInfo['category_name']) {
                    $categoryTree[] = $categoryInfo;    
                    if(!isset($tmp[$categoryInfo['id']])) {
                        Category::insert($categoryInfo);
                        $tmp[$categoryInfo['id']] = 1;
                    }
                }
            }
            $midMicro = explode(' ',microtime());
            $mid = $midMicro[1] + $midMicro[0];
            echo ($pageSize * $i).' '.$mid."\n";
        }
        $endMicro = explode(' ',microtime());
        $end = $endMicro[1] + $endMicro[0];
        echo 'product import '.$productNum.' ; goods import '.$goodsNum.' .';
        echo 'execute '.number_format($end - $start,2).' s';
    }
}
