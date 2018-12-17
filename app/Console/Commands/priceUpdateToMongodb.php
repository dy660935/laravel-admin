<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class priceUpdateToMongodb extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */

    protected $signature = 'priceUpdateToMongodb {--website=} {--startPage=}';

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

        $startMicro = explode( ' ' , microtime() );
        $start = $startMicro[ 1 ] + $startMicro[ 0 ];

        #传入的网站
        $website = $this->option( 'website' );

        if( !$website ) {
            $website = 'kaola';
        }

        #通过网站查对应分商品

        //查询goods表的总条数
        $goods_count = DB::table( "fb_goods" )
            ->where( [ 'goods_status' => 1 , 'orignal_website' => $website ] )
            ->count();

        #开始位置
        $startPage = $this->option( 'startPage' );

        #定义每次修改的条数
        $pageSize = 500;

        #起始位置
        if( !$startPage ) {
            $startPage = 0;
        } else {
            $startPage = intval( ceil( $startPage / $pageSize ) );
        }

        #算出要循环的次数
        $for_count = intval( ceil( $goods_count / $pageSize ) );

        for( $i = $startPage ; $i <= $for_count ; $i++ ) {

            #查出limit下的商品
            $goods_info = DB::table( 'fb_goods' )
                ->where( [ 'goods_status' => 1 , 'orignal_website' => $website ] )
                ->offset( $i * $pageSize )
                ->limit( $pageSize )
                ->get( [ 'original_goods_url' , 'original_goods_id' , 'main_code' , 'price_updated_at' , 'update_type' ] );

            $now = time();

            #循环插入mongodb前判断商品是否存在更新时间是否到期
            foreach( $goods_info as $goods_k => $goods_v ) {

                #如果每天更新并且上次更新时间大于一天 或者 每周更新并且更新时间大于7天插入mongodb进行更新
                if( ( $goods_v->update_type = 1 && $now - strtotime( $goods_v->price_updated_at ) > 3600 * 24 ) || (
                    $goods_v->update_type = 2 && $now - strtotime( $goods_v->price_updated_at ) > 3600 * 24 * 7 ) ) {

                    $table = $website . '_update';

                    //查询mongodb是否有该条数据有则修改
                    $mongodb = DB::connection( 'mongodb' )
                        ->collection( $table )
                        ->where( 'good_id' , $goods_v->original_goods_id )
                        ->first();

                    if( !empty( $mongodb ) ) {
                        if( $mongodb[ 'is_update' ] != 2 ) {
                            //更新数据
                            $data = [
                                'good_id' => $goods_v->original_goods_id ,
                                'good_url' => $goods_v->original_goods_url ,
                                'main_code' => $goods_v->main_code ,
                                'is_update' => '2' ,
                                'update_type' => "$goods_v->update_type" ,
                                'crawl_update' => '' ,
                            ];
                            $result = DB::connection( 'mongodb' )
                                ->collection( $table )
                                ->where( 'good_id' , $mongodb[ 'good_id' ] )
                                ->update( $data , [ 'upsert' => true ] );

                            //                            echo "$website" , ' ' , $mongodb[ 'good_id' ];
                        }


                    } else {

                        //插入数据
                        $res = DB::connection( 'mongodb' )
                            ->collection( $table )
                            ->insert( [
                                'good_id' => $goods_v->original_goods_id ,
                                'good_url' => $goods_v->original_goods_url ,
                                'main_code' => $goods_v->main_code ,
                                'is_update' => '2' ,
                                'update_type' => "$goods_v->update_type" ,
                                'crawl_update' => '' ,
                            ] );
                    }
                }
            }
            $midMicro = explode( ' ' , microtime() );
            $mid = $midMicro[ 1 ] + $midMicro[ 0 ];
            echo ( $pageSize * $i ) . ' ' . $mid . ' ' . $this->_getExcuteTime( $start , $mid ) . "s" . "\r\n";

        }
        echo "$website" , ' ' , "success" . "\r\n";
    }

    private function _getExcuteTime( $start , $end )
    {
        return round( $end - $start , 3 );
    }

}
