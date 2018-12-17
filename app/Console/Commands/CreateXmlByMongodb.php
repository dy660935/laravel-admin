<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;

class CreateXmlByMongodb extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'CreateXmlByMongodb';

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
        $startMicro = explode(' ',microtime());
        $start = $startMicro[1] + $startMicro[0];
        //echo microtime()."\n";
        //$res = DB::connection('mongodb') -> collection('kaola')->where('comment_count','>=',1000)->get()->toArray();
        $res = DB::connection('mongodb') -> collection('jd')->where('comment_count','>=',1000)->take(10)->get()->toArray();
        $productList = $goodsList = $categoryTree = $productInfo = $goodsInfo = [];
        $productNum = $goodsNum = 0;
        echo '<?xml version="1.0" encoding="utf-8"?>'."\n";
        echo '<sphinx:docset>'."\n";
        echo "\t".'<sphinx:schema>'."\n";
        echo "\t".'<sphinx:field name="goods_name"/>'."\n";
        echo "\t".'<sphinx:attr name="original_goods_id" type="int"/>'."\n";
        echo "\t".'</sphinx:schema>'."\n";
        foreach($res as $k => $v) {
            echo "\t".'<sphinx:document id="'.$v['good_id'].'">'."\n";
            //echo '<orignal_website>'.$v['good_from'].'</orignal_website>';
            //echo '<original_goods_url>'.$v['good_url'].'</original_goods_url>';
            echo "\t"."\t".'<original_goods_id>'.$v['good_id'].'</original_goods_id>'."\n";
            //echo '<market_price>'.$v['add_to_field'][0]['original_cost'].'</market_price>';
            //echo '<shop_price>'.$v['add_to_field'][0]['price_text'].'</shop_price>';
            echo "\t"."\t".'<goods_name>'.str_replace('&','',$v['title']).'</goods_name>'."\n";
            echo "\t".'</sphinx:document>'."\n";
            $goodsNum++;
        }
        echo '</sphinx:docset>'."\n";
        /*echo '<?xml version="1.0" encoding="utf-8"?>';
        echo '<docset>';
        echo '<schema>';
        echo '<field name="orignal_website" type="string"/>';
        echo '<field name="original_goods_url" type="string"/>';
        echo '<field name="market_price" type="int"/>';
        echo '<field name="shop_price" type="int"/>';
        echo '<field name="goods_name"/>';
        echo '<attr name="original_goods_id" type="int"/>';
        echo '</schema>';
        foreach($res as $k => $v) {
            echo '<document id="'.$v['good_id'].'">';
            echo '<orignal_website>'.$v['good_from'].'</orignal_website>';
            echo '<original_goods_url>'.$v['good_url'].'</original_goods_url>';
            echo '<original_goods_id>'.$v['good_id'].'</original_goods_id>';
            echo '<market_price>'.$v['add_to_field'][0]['original_cost'].'</market_price>';
            echo '<shop_price>'.$v['add_to_field'][0]['price_text'].'</shop_price>';
            echo '<goods_name>'.str_replace('&','',$v['title']).'</goods_name>';
            echo '</document>';
            $goodsNum++;
        }
        echo '</docset>';*/


        /*echo '<?xml version="1.0" encoding="utf-8"?>';
        echo '<docset>';
        foreach($res as $k => $v) {
            echo '<document id="'.$v['good_id'].'">';
            echo '<orignal_website>'.$v['good_from'].'</orignal_website>';
            echo '<original_goods_url>'.$v['good_url'].'</original_goods_url>';
            echo '<original_goods_id>'.$v['good_id'].'</original_goods_id>';
            echo '<market_price>'.$v['add_to_field'][0]['original_cost'].'</market_price>';
            echo '<shop_price>'.$v['add_to_field'][0]['price_text'].'</shop_price>';
            echo '<goods_name>'.str_replace('&','',$v['title']).'</goods_name>';
            echo '</document>';
            $goodsNum++;
        }
        echo '</docset>';*/
        $endMicro = explode(' ',microtime());
        $end = $endMicro[1] + $endMicro[0];
        //echo 'goods import '.$goodsNum.' .';
        //echo 'execute '.number_format($end - $start,2).' s';
    }
}
