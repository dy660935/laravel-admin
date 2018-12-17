<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;

class PromotionExplode extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'PromotionExplode';

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
        /*$sql = "select promotion_info from fb_goods where promotion_info != ''";
        $count = DB::select($sql);*/
        $sql = "select count(*) countNum from fb_goods where promotion_info != ''";
        $count = DB::select($sql);
        $count = $count[0]->countNum;
        echo $count;
        $pageSize = 1000;
        $page = ceil($count/$pageSize);
        $startPage = 0;
        $promotionListTag = $promotionContentsList = [];
        $promotionProductCount = 0;
        for($i = $startPage; $i < $page; $i++) {
            $offset = $i * $pageSize;
            $sql = "select promotion_info from fb_goods where promotion_info != '' limit {$offset},{$pageSize}";
            $res = DB::select($sql);
            foreach($res as $k => $v) {
                $promotionInfo = json_decode($v->promotion_info,true);
                $hasPromotion = 0;
                foreach($promotionInfo['price_tags'] as $m => $n) {
                    if(preg_match('/仅可购买/',$n)) continue;
                    $promotionListTag[$n] = 1;
                    $hasPromotion = 1;
                }
                foreach($promotionInfo['promotion_contents'] as $m => $n) {
                    //if(preg_match('/超出数量以结算价为准/',$n)) continue;
                    //满减
                    if(preg_match('/满[0-9]+元减[0-9]+元/',$n)) continue;

                    //循环满减
                    if(preg_match('/每满[0-9]+元(,|，)可减[0-9]+元现金/',$n)) continue;

                    //满赠
                    if(preg_match('/满[0-9]+元即赠热销商品(,|，)赠完即止/',$n)) continue;
                    if(preg_match('/满[0-9\.元、]+可得相应赠品，赠完即止，请在购物车点击领取/',$n)) continue;

                    //m件n折
                    if(preg_match('/满[0-9]+件(,|，)总价打[0-9\.]+折/',$n)) continue;

                    //m元n件
                    if(preg_match('/[0-9]+元选[0-9]+件(,|，)每个商品最多购买[0-9]+件/',$n)) continue;
                    if(preg_match('/[0-9]+元选[0-9]+件/',$n)) continue;

                    //赠京豆
                    if(preg_match('/赠[0-9,]+京豆/',$n)) continue;

                    //加价购
                    if(preg_match('/满[0-9\.]+元另加[0-9\.]+元/',$n)) continue;

                    //买赠
                    if(preg_match('/（条件：购买[0-9]+件及以上(,|，)赠完即止）/',$n)) continue;
                    if(preg_match('/满[0-9]+件即赠热销商品，赠完即止，请在购物车点击领取/',$n)) continue;
                    if(preg_match('/（PLUS会员专享(,|，)赠完即止）/',$n)) continue;
                    if(preg_match('/（赠完即止）/',$n)) continue;

                    //m件n元
                    if(preg_match('/购买[0-9\-]+件时可享受单件价￥[0-9\.]+，其他数量以结算价为准/',$n)) continue;
                    if(preg_match('/购买至少[0-9]+件时可享受单件价￥[0-9]+(,|，)不足时以结算价为准/',$n)) continue;

                    //换购
                    if(preg_match('/购买[0-9\-]+件可优惠换购热销商品/',$n)) continue;

                    //满m件免n
                    if(preg_match('/满[0-9]+件，立减最低[0-9]+件商品价格/',$n)) continue;
                    $promotionContentsList[$n] = 1;
                }
                if($hasPromotion) {
                    $promotionProductCount++;
                }
            }
        }
        print_r($promotionListTag);
        echo $promotionProductCount;
        print_r($promotionContentsList);
    }
}
