<?php

namespace App\Console\Commands;

use App\Price;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class priceUpdateFromMongodb extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'priceUpdateFromMongodb {--website=} {--startPage=}';

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

        //通过mysql拿到所有网站拼接对应的表名
        $website = $this->option( 'website' );

        #mongodb表名
        if( !$website ) {
            $table = 'kaola_update';
        } else {
            if( stristr( $website , '_update' ) !== false ) {
                $table = $website;
            } elseif( mb_strlen( stristr( $website , '_' ) ) == 1 ) {
                $table = $website . 'update';
            } else {
                $table = $website . '_update';
            }
        }

        #mysql网站简称
        $website_abbreviation = mb_substr( $table , 0 , mb_strrpos( $table , '_' ) );


        //查询对应网站的修改的总条数
        $count = DB::connection( 'mongodb' )
            ->collection( $table )
//            ->where( 'is_update' , '1' )
            ->where( 'is_update' , '2' )
            ->count();

        #开始位置
        $startPage = $this->option( 'startPage' );

        #定义每次修改的条数
        $pageSize = 1000;

        #起始位置
        if( !$startPage ) {

            $startPage = 0;

        } else {

            $startPage = intval( ceil( $startPage / $pageSize ) );

        }


        #算出要循环的次数
        $for_count = intval( ceil( $count / $pageSize ) );

        $original_website_id = DB::table( 'fb_website' )
            ->where( [ 'website_abbreviation' => $website_abbreviation ] )
            ->first( [ 'id' ] );


        for( $i = $startPage ; $i <= $for_count ; $i++ ) {
            //拿到对应的表名去mongodb中查对应的表的数据条件对应的is_update等于1的数据
            $mongodb = DB::connection( 'mongodb' )
                ->collection( $table )
                ->where( 'is_update' , '2' )
//                ->where( 'is_update' , '1' )
                ->skip( $i * $pageSize )
                ->take( $pageSize )
                ->get( [ 'good_id' ] )
                ->toArray();

            if( !empty( $mongodb ) ) {
                foreach( $mongodb as $m_k => $m_v ) {
                    //拿到对应的good_id去mongodb中查对应的表价格信息
                    $price_old = DB::connection( 'mongodb' )
                        ->collection( $website_abbreviation )
                        ->where( 'good_id' , $m_v[ 'good_id' ] )
                        ->first( [ 'good_id' , 'add_to_field' , 'is_onsell' , 'has_stock' , 'quantity' , 'price_tags' , 'sell_count' ] );

                    #取最后更新的价格数据
                    $j = count( $price_old[ 'add_to_field' ] ) - 1;

                    //数据不为空拼接数据获取market_price和shop_price
                    $price_old[ 'orignal_website_id' ] = $original_website_id->id;
                    $price_old[ 'is_postage' ] = $price_old[ 'add_to_field' ][ $j ][ 'is_postage' ];
                    $price_old[ 'postage_price' ] = $price_old[ 'add_to_field' ][ $j ][ 'postage_price' ];
                    $price_old[ 'market_price' ] = $price_old[ 'add_to_field' ][ $j ][ 'original_cost' ];
                    $price_old[ 'original_price' ] = $price_old[ 'add_to_field' ][ $j ][ 'price_text' ];
                    $price_old[ 'is_cross_border_tax_in' ] = $price_old[ 'add_to_field' ][ $j ][ 'is_cross_border_tax_in' ];
                    $price_old[ 'cross_border_tax' ] = $price_old[ 'add_to_field' ][ $j ][ 'cross_border_tax' ];

                    $price_old[ 'is_import_fee_in' ] = isset( $price_old[ 'add_to_field' ][ $j ][ 'is_import_fee_in' ] ) ?
                        ( $price_old[ 'add_to_field' ][ $j ][ 'is_import_fee_in' ] > 0 ?
                            $price_old[ 'add_to_field' ][ $j ][ 'is_import_fee_in' ]:
                            2 )
                        :
                        2;

                    $price_old[ 'import_fee' ] = isset( $price_old[ 'add_to_field' ][ $j ][ 'import_fee' ] ) ?
                        ( $price_old[ 'add_to_field' ][ $j ][ 'import_fee' ] > 0 ?
                            $price_old[ 'add_to_field' ][ $j ][ 'import_fee' ]:
                            0 )
                        :
                        0;


                    $price_old[ 'is_local_tax_in' ] = isset( $price_old[ 'add_to_field' ][ $j ][ 'is_local_tax_in' ] ) ?
                        ( $price_old[ 'add_to_field' ][ $j ][ 'is_local_tax_in' ] > 0 ?
                            $price_old[ 'add_to_field' ][ $j ][ 'is_local_tax_in' ]:
                            2 )
                        :
                        2;

                    $price_old[ 'duty_free_price' ] = isset( $price_old[ 'add_to_field' ][ $j ][ 'duty_free_price' ] ) ?
                        ( $price_old[ 'add_to_field' ][ $j ][ 'duty_free_price' ] > 0 ?
                            $price_old[ 'add_to_field' ][ $j ][ 'duty_free_price' ]:
                            0 )
                        :
                        0;

                    $price_old[ 'vip_price' ] = isset( $price_old[ 'add_to_field' ][ $j ][ 'vip_price' ] ) ?
                        ( $price_old[ 'add_to_field' ][ $j ][ 'vip_price' ] > 0 ?
                            $price_old[ 'add_to_field' ][ $j ][ 'vip_price' ]:
                            0 ):
                        0;

                    $price_old[ 'local_tax_in_price' ] = isset( $price_old[ 'add_to_field' ][ $j ][ 'local_tax_in_price' ] ) ?
                        ( $price_old[ 'add_to_field' ][ $j ][ 'local_tax_in_price' ] > 0 ?
                            $price_old[ 'add_to_field' ][ $j ][ 'local_tax_in_price' ]:
                            0 ):
                        0;

                    $price_old[ 'tax_refund_price' ] = isset( $price_old[ 'add_to_field' ][ $j ][ 'tax_refund_price' ] ) ?
                        ( $price_old[ 'add_to_field' ][ $j ][ 'tax_refund_price' ] > 0 ?
                            $price_old[ 'add_to_field' ][ $j ][ 'tax_refund_price' ]:
                            0 )
                        :
                        0;

                    $price_old[ 'is_oversea' ] = !isset( $price_old[ 'add_to_field' ][ $j ][ 'is_oversea' ] ) ?
                        1:
                        $price_old[ 'add_to_field' ][ $j ][ 'is_oversea' ];

                    $price_old[ 'is_onsell' ] = !isset( $price_old[ 'add_to_field' ][ $j ][ 'is_onsell' ] ) ?
                        1:
                        $price_old[ 'add_to_field' ][ $j ][ 'is_onsell' ];

                    $price_old[ 'has_stock' ] = !isset( $price_old[ 'add_to_field' ][ $j ][ 'has_stock' ] ) ?
                        1:
                        $price_old[ 'add_to_field' ][ $j ][ 'has_stock' ];

                    $stock_number = isset( $price_old[ 'quantity' ] ) ?
                        ( $price_old[ 'quantity' ] > 0 ?
                            $price_old[ 'quantity' ]:
                            0 ):
                        100;

                    $price_old[ 'currency_genre' ] = $price_old[ 'add_to_field' ][ $j ][ 'currency' ];

                    //优惠信息存储为json
                    $goodsInfo = [];
                    if( isset( $price_old[ 'price_tags' ] ) && $price_old[ 'price_tags' ] ) {
                        if( is_array( $price_old[ 'price_tags' ] ) ) {
                            $goodsInfo[ 'price_tags' ] = $price_old[ 'price_tags' ];
                        } else {
                            $goodsInfo[ 'price_tags' ] = [ $price_old[ 'price_tags' ] ];
                        }
                    }
                    if( isset( $price_old[ 'price_tags' ] ) && $price_old[ 'price_tags' ] ) {
                        $goodsInfo[ 'price_tags' ] = $price_old[ 'price_tags' ];
                    }
                    if( $goodsInfo ) {
                        $goodsInfo = json_encode( $goodsInfo[ 'price_tags' ] );
                    } else {
                        $goodsInfo = '';
                    }


                    $price_model = new Price();

                    $price_new = $price_model->getShopPrice( $price_old );

                    //查询该条数据在mysql表中的唯一主键
                    $good = DB::table( 'fb_goods' )
                        ->where( [ 'original_goods_id' => $m_v[ 'good_id' ] ] )
                        ->where( [ 'orignal_website' => $website_abbreviation ] )
                        ->where( [ 'goods_status' => 1 ] )
                        ->first( [ 'id' ] );

                    if(!empty($good)){
                        $data = [
                            'goods_id' => $good->id ,
                            'market_price' => intval( $price_new[ 'market_price' ] ) ,
                            'shop_price' => intval( $price_new[ 'shop_price' ] ) ,
                            'is_oversea' => $price_old[ 'is_oversea' ] ,
                            'stock_number' => $stock_number ,
                            'is_onsell' => $price_old[ 'is_onsell' ] ,
                            'orignal_website_id' => $original_website_id->id ,
                            'has_stock' => $price_old[ 'has_stock' ] ,
                            'is_postage' => $price_old[ 'is_postage' ] ,
                            'postage_price' => $price_old[ 'postage_price' ] ,
                            'cross_border_tax' => $price_old[ 'cross_border_tax' ] ,
                            'is_cross_border_tax_in' => $price_old[ 'is_cross_border_tax_in' ] ,
                            'promotion_info' => $goodsInfo ,
                            'is_import_fee_in' => $price_old[ 'is_import_fee_in' ] ,
                            'import_fee' => $price_old[ 'import_fee' ] ,
                            'vip_price' => $price_old[ 'vip_price' ] ,
                            'is_local_tax_in' => $price_old[ 'is_local_tax_in' ] ,
                            'local_tax_in_price' => $price_old[ 'local_tax_in_price' ] ,
                            'duty_free_price' => $price_old[ 'duty_free_price' ] ,
                            'tax_refund_price' => $price_old[ 'tax_refund_price' ] ,
                            'good_url_lost' => 1 ,
                            'currency_genre' => $price_old[ 'currency_genre' ] ,
                            'original_price' => $price_old[ 'original_price' ] ,
                            'created_at' => date( "Y-m_d H:i:s" , time() ) ,
                        ];

                        //更新mysql数据表fb_goods_price_update
                        $res = DB::table( 'fb_goods_price_update' )
                            ->insert( $data );

                        //对goods表做对应的更新
                        if( $res ) {
                            unset( $data[ 'goods_id' ] , $data[ 'created_at' ] );
                            $data[ 'price_updated_at' ] = date( "Y-m_d H:i:s" , time() );
                            //更新goods表
                            $res1 = DB::table( 'fb_goods' )
                                ->where( [ 'id' => $good->id ] )
                                ->update( $data );
                        }
                    }else{
                        echo "该条商品已下架\r\n";
                    }

                }
            } else {

                echo $website_abbreviation . "没有可更新的数据\r\n";
            }
            $midMicro = explode( ' ' , microtime() );
            $mid = $midMicro[ 1 ] + $midMicro[ 0 ];
            echo ( $pageSize * $i ) . ' ' . $mid . ' ' . $this->_getExcuteTime( $start , $mid ) . "s" . "\r\n";
        }

        echo $website_abbreviation . "success";


    }

    private function _getExcuteTime( $start , $end )
    {
        return round( $end - $start , 3 );
    }
}
