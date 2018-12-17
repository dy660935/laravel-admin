<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;
use Illuminate\Support\Facades\Redis;
use App\Website;
class ImportCount extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ImportCount';

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
        $websiteObj = new Website;
        $websiteHash = $websiteObj->getWebsiteAry();
        $redisKey = 'ImportCount_'.env('APP_ENV');
        $count = [];
        $count['count'] = 0;
        $sql = "select count(*),orignal_website_id from fb_goods_specs group by orignal_website_id";
        $specCount = DB::select($sql);
        if($specCount) {
            foreach($specCount as $k => $v) {
                $count['spec'][$websiteHash[$v->orignal_website_id]['website_abbreviation']] = 1;
            }
        }
        $sql = "select count(*),website_id from fb_keyword_goods group by website_id";
        $keywordCount = DB::select($sql);
        if($keywordCount) {
            foreach($keywordCount as $k => $v) {
                $count['keyword'][$websiteHash[$v->website_id]['website_abbreviation']] = 1;
            }
        }
        $sql = "select count(*) num,orignal_website_id from fb_goods group by orignal_website_id";
        $goodsCount = DB::select($sql);
        if($goodsCount) {
            foreach($goodsCount as $k => $v) {
                $count['goods'][$websiteHash[$v->orignal_website_id]['website_abbreviation']] = $v->num;
                $count['count'] += $v->num;
            }
        }
        $count['update'] = date('Y-m-d H:i:s');
        Redis::set($redisKey,json_encode($count));
    }

}
