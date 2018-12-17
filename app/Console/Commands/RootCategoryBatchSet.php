<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Category;
use DB;
use App\Product;

class RootCategoryBatchSet extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'RootCategoryBatchSet';

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
        echo microtime()."\n";
        $sql = "update fb_product set root_category_id = category_id where product_status = 1 and `root_category_id` = 0 and `category_id` != 0;";
        $categoryObj = new Category;
        $categoryObj->setCategoryStatus(0);
        $categoryThridAry = $categoryObj->getCategoryThirdAry();
        $categoryThridIds = implode(',',$categoryThridAry);
        $sql = "select id,category_id from fb_product where category_id in ({$categoryThridIds}) and product_status = 1 and `category_id` != 0;";
        $sql = "select id,category_id from fb_product where category_id in ({$categoryThridIds}) and  `category_id` != 0;";
        $productList = DB::select($sql);
        if($productList) {
            foreach($productList as $k => $v) {
                $rootCategoryId = $categoryObj->getRootCategoryIdByThridCategoryId($v->category_id);
                if($rootCategoryId) {
                    Product::where(['id'=>$v->id])->update(['root_category_id'=>$rootCategoryId]);
                }
            }
        }
        //print_r($thridFirstHash);
        echo microtime()."\n";
    }
}
