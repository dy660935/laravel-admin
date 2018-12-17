<?php

namespace App\Admin\Controllers;

use App\Brand;
use App\Category;
use App\CategoryChangeLog;
use App\Currency;
use App\Goods;
use App\GoodsSpecs;
use App\Keyword;
use App\KeywordGoods;
use App\Libs\sphinx\SphinxClient;
use App\Price;
use App\Product;
use App\Specs;
use App\Website;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductController extends CommonController
{
    /*
     * 商品列表
     */


    public function index( Request $request )
    {

        $search = $request->input();

        $where = [];

        if( isset( $search[ 'product_name' ] ) ) {
            $where[] = [ 'product_name' , 'like' , '%' . $search[ 'product_name' ] . '%' ];
        }

        if( isset( $search[ 'brand_name' ] ) ) {
            $where[] = [ 'brand_id' , '=' , $search[ 'brand_name' ] ];
        }

        if( isset( $search[ 'status' ] ) ) {
            $where[] = [ 'product_status' , '=' , $search[ 'status' ] ];
        }

        if( isset( $search[ 'is_import' ] ) && in_array( $search[ 'is_import' ] , [ 0 , 1 ] ) ) {
            $where[] = [ 'is_import' , '=' , $search[ 'is_import' ] ];
        }

        //        DB::connection()->enableQueryLog();#开启执行日志
        $product = Product::with( [ 'category' , 'goods' , 'brand' , 'website' ] )
            ->where( [ 'fb_product.is_deleted' => 1 ] )
            ->where( $where )
            ->orderBy( 'product_weight' , 'desc' )
            ->paginate( 20 );
        //        dd(DB::getQueryLog());

        //        dd($product);

        if( empty( $search[ 'product_name' ] ) ) {
            $search[ 'product_name' ] = '';
        }
        if( empty( $search[ 'brand_name' ] ) ) {
            $search[ 'brand_name' ] = '';
        }
        if( empty( $search[ 'status' ] ) ) {
            $search[ 'status' ] = '';
        }
        if( !isset( $search[ 'is_import' ] ) ) {
            $search[ 'is_import' ] = '2';
        }

        $product->products_name = $search[ 'product_name' ];
        $product->brands_name = $search[ 'brand_name' ];
        $product->status = $search[ 'status' ];

        $brand_data = Brand::where( [ 'brand_status' => 1 , 'is_deteled' => 1 ] )
            ->get( [ 'id' , 'brand_chinese_name' ] );

        foreach( $product as $pro_k => $pro_v ) {

            //            if(strpos($pro_v['product_image'],'http') !==false){
            //                $product[$pro_k]['product_image']=$pro_v['product_image'];
            //            }else{
            //                $product[$pro_k]['product_image']='http://six.qqzdj.com.cn/'.$pro_v['product_image'];
            //            }

            $product[ $pro_k ][ 'product_image' ] = $this->imageCompatible( $pro_v[ 'product_image' ] );

            if( $pro_v->goods ) {

                $specs = $pro_v->goods->good_specs;

                if( $specs ) {

                    $good_specs = implode( ',' , json_decode( $pro_v->goods->good_specs , true ) );

                    $product[ $pro_k ]->goods->good_specs = $good_specs;

                } else {

                    $product[ $pro_k ]->goods->good_specs = '';

                }
            }

        }
        return view( '/admin/product/index' , compact( 'product' , 'search' , 'brand_data' ) );
    }


    /*
     * 创建商品添加
     */
    public function create()
    {
        //查询品牌
        $brand = DB::table( 'fb_brand' )->where( 'brand_status' , 1 )->get();
        //查询分类信息
        $category = DB::table( 'fb_category' )->where( 'category_status' , 1 )->get();

        //属性值展示
        $attribute_name_enum = DB::table( 'fb_attribute_name_enum' )->where( 'attribute_name_status' , 1 )->get();

        return view( '/admin/product/add' , compact( 'brand' , 'category' , 'attribute_name_enum' ) );
    }

    /*
     * 执行商品导入
     */
    public function import( Request $request )
    {

        $website_name = $request->post( "website_name" );

        $product_id = $request->post( "product_id" );

        //        $product_id="'".$product_id''";

        $res = DB::connection( 'mongodb' )
            ->collection( $website_name )
            ->where( [ "good_id" => $product_id , "good_from" => $website_name , "is_onsell" => 1 ] )
            ->first();

        //处理数据
        if( !$res ) {
            return [ 'font' => '请确认你的数据是否正确' , 'code' => 2 ];
        } else {
            //商品价格
            $result = array_pop( $res[ 'add_to_field' ] );

            $res[ 'shop_price' ] = $result[ 'price_text' ];

            $res[ 'market_price' ] = $result[ 'original_cost' ];

            $res[ 'currency_genre' ] = $result[ 'currency' ];

            return [ 'font' => '导入成功' , 'code' => 1 , "data" => $res ];
        }
    }

    /*
     * 执行商品添加
     */
    public function add( Request $request )
    {

        //验证
        $this->validate( $request , [
            'pro_name' => 'required|min:2' ,
            'original_product_id' => 'required' ,
        ] , [
            'pro_name.required' => '商品名称不能为空' ,
            'pro_name.min' => '商品名称最小两个字符' ,
        ] );;

        //逻辑
        $data = \request()->all();

        //根据获取的品牌名称 获取品牌的id
        if( $data[ 'pro_tab' ] ) {
            $data[ 'pro_tab' ] = implode( ',' , $data[ 'pro_tab' ] );

        }
        if( $data[ 'search' ] ) {

            $data[ 'search' ] = implode( ',' , $data[ 'search' ] );

        }
        //        dd($data);
        //执行商品添加product
        $original_product_id = DB::table( 'fb_product' )->where( 'original_product_id' , $data[ 'original_product_id' ] )->first( [ 'id' ] );

        if( $original_product_id ) {

            return [ 'font' => '该数据已经添加' , 'code' => 2 ];

        } else {
            $product_id = DB::table( 'fb_product' )->insertGetId( [
                'brand_id' => $data[ 'brand_id' ] ,
                'category_id' => $data[ 'category_id' ] ,
                'product_sn' => $data[ 'product_sn' ] ,
                'original_product_id' => $data[ 'original_product_id' ] ,
                'product_name' => $data[ 'pro_name' ] ,
                'currency_genre' => $data[ 'currency_genre' ] ,
                'product_describle' => $data[ 'pro_name' ] ,
                'product_status' => $data[ 'product_status' ] ,
                'product_image' => $data[ 'img1' ] ,
                'product_keywords' => $data[ 'search' ] ,
                'product_label' => $data[ 'pro_tab' ] ,
                'created_at' => date( 'Y-m-d H:i:s' , time() ) ,
            ] );

        }
        if( $data[ 'attribute_name' ] && $data[ 'attribute_value' ] ) {
            //查询属性
            $attribute = DB::table( 'fb_attribute_name_enum' )->where( 'attribute_name' , $data[ 'attribute_name' ] )->first( [ 'id' ] );

            if( $attribute ) {
                $attribute = get_object_vars( $attribute );
                $attribute_name_id = $attribute[ 'id' ];
            } else {
                //执行属性，属性值的添加
                $attribute_name_id = DB::table( 'fb_attribute_name_enum' )->insertGetId( [
                    'attribute_name' => $data[ 'attribute_name' ] ,
                    'created_at' => date( 'Y-m-d H:i:s' , time() ) ,
                ] );
            }

            $attribute_value = DB::table( 'fb_attribute_value_enum' )->where( 'attribute_value' , $data[ 'attribute_value' ] )->first( [ 'id' ] );

            if( !$attribute_value ) {
                //执行属性，属性值的添加
                $attribute_value_id = DB::table( 'fb_attribute_value_enum' )->insertGetId( [
                    'attribute_value' => $data[ 'attribute_value' ] ,
                    'attribute_id' => $attribute_name_id ,
                    'created_at' => date( 'Y-m-d H:i:s' , time() ) ,
                ] );
            } else {
                $attribute_value = get_object_vars( $attribute_value );
                $attribute_value_id = $attribute_value[ 'id' ];
            }

            $res[ 'attribute_name_id' ] = $attribute_name_id;
            $res[ 'attribute_value_id' ] = $attribute_value_id;
        }
        $res[ 'product_id' ] = $product_id;

        if( $product_id ) {
            return [ 'font' => '添加成功' , 'code' => 1 , 'data' => $res ];
        }
    }

    private function _commonData()
    {
        $websiteObj = new Website();
        $websiteHash = $websiteObj->getWebsiteAry( [ 'id' , 'website_abbreviation' , 'website_name' , 'pay_way' ] );
        $matchMode = [ '已匹配' , '未匹配' , '无法匹配' ];
        $pageSizeAry = [ 500 , 200 , 100 , 50 , 20 ];
        $brandObj = new Brand();
        $brandHash = $brandObj->getbrandHash();
        $currencyObj = new Currency();
        $currencyHash = $currencyObj->getCurrencyHash();
        $categoryObj = new Category;
        $categoryAry = $categoryObj->getCategoryAry();
        foreach( $categoryAry[ 'category3' ] as $k => $v ) {
            $categoryAry[ 'category3Hash' ][ $v[ 'id' ] ] = $v[ 'category_name' ];
        }
        return $commonData = [ 'websiteHash' => $websiteHash , 'matchMode' => $matchMode , 'brandHash' => $brandHash , 'categoryAry' => $categoryAry , 'pageSizeAry' => $pageSizeAry , 'imageCdnUrl' => config( 'filesystems.image_cdn_url' ) , 'currencyHash' => $currencyHash ];
    }

    private function _getCNY( $goodsInfo , $currencyHash )
    {
        if( $goodsInfo->currency_genre == 'CNY' ) {
            return $goodsInfo->shop_price;
        } else {
            if( isset( $currencyHash[ $goodsInfo->currency_genre ] ) ) {
                return round( $goodsInfo->shop_price * $currencyHash[ $goodsInfo->currency_genre ] / 100 , 3 );
            } else {
                return '未获取到汇率';
            }
        }
    }

    public function goodsMatch( Request $request )
    {
        $ajax = $request->ajax();
        $goodsMatchParams = json_decode( $request->session()->get( 'goodsMatchParams' ) , true );
        $goodsObj = new Goods;
        $commonData = $this->_commonData();
        $websiteAry = $request->input( 'website' );
        $isMaster = $request->input( 'is_master' , '2' );
        if( !in_array( $isMaster , [ 0 , 1 ] ) ) {
            $isMaster = 2;
        }
        if( !$websiteAry ) {
            //$websiteAry = array_keys($commonData['websiteHash']);
            $websiteAry = [];
        }
        $brandId = $request->input( 'brand' );
        $cate1Id = $request->input( 'cate1' );
        $cate2Id = $request->input( 'cate2' );
        $cate3Id = $request->input( 'cate3' );
        $page = $request->input( 'page' );
        if( !$page ) {
            $page = 1;
        }

        $mode = $request->input('mode');
        $keyword = $request->input('keyword');
        $pagesize = intval($request->input('pagesize'));
        if(!in_array($pagesize,$commonData['pageSizeAry'])) {
            $pagesize = 50;
        }
        $goodsList = [];
        $hasMaster = 0;
        if( !$websiteAry && !$brandId && !$cate1Id && !$cate2Id && !$cate3Id && !$keyword ) {
            if( isset( $goodsMatchParams[ 'websiteAry' ] ) && $goodsMatchParams[ 'websiteAry' ] ) {
                $websiteAry = $goodsMatchParams[ 'websiteAry' ];
            }
            if( isset( $goodsMatchParams[ 'brandId' ] ) && $goodsMatchParams[ 'brandId' ] ) {
                $brandId = $goodsMatchParams[ 'brandId' ];
            }
            if( isset( $goodsMatchParams[ 'cate1Id' ] ) && $goodsMatchParams[ 'cate1Id' ] ) {
                $cate1Id = $goodsMatchParams[ 'cate1Id' ];
            }
            if( isset( $goodsMatchParams[ 'cate2Id' ] ) && $goodsMatchParams[ 'cate2Id' ] ) {
                $cate2Id = $goodsMatchParams[ 'cate2Id' ];
            }
            if( isset( $goodsMatchParams[ 'cate3Id' ] ) && $goodsMatchParams[ 'cate3Id' ] ) {
                $cate3Id = $goodsMatchParams[ 'cate3Id' ];
            }
            if( isset( $goodsMatchParams[ 'pagesize' ] ) && $goodsMatchParams[ 'pagesize' ] ) {
                $pagesize = $goodsMatchParams[ 'pagesize' ];
            }
            if( isset( $goodsMatchParams[ 'keyword' ] ) && $goodsMatchParams[ 'keyword' ] ) {
                $keyword = $goodsMatchParams[ 'keyword' ];
            }
            if( isset( $goodsMatchParams[ 'isMaster' ] ) ) {
                $isMaster = $goodsMatchParams[ 'isMaster' ];
            }

        }
        $searchd = 0;
        if( $keyword ) {
            $cl = new SphinxClient ();
            $mode = isset( $_GET[ 'mode' ] ) ?
                intval( $_GET[ 'mode' ] ):
                SPH_MATCH_ALL;
            $cl->SetServer( env( 'SPHINX_HOST' ) , 9312 );
            $cl->SetConnectTimeout( 3 );
            $cl->SetArrayResult( true );
            $cl->SetFilter( 'orignal_website_id' , $websiteAry );
            if( $brandId ) {
                $cl->SetFilter( 'brand_id' , [ $brandId ] );
            }
            if( $cate3Id ) {
                $cl->SetFilter( 'category_id' , [ $cate3Id ] );
            }
            if( $isMaster != 2 ) {
                $cl->SetFilter( 'is_master' , [ $isMaster ] );
            }
            if( !in_array( $mode , [ SPH_MATCH_ALL , SPH_MATCH_ANY ] ) ) {
                $mode = SPH_MATCH_ALL;
            }
            $cl->SetMatchMode( $mode );
            $cl->SetSortMode( SPH_SORT_EXTENDED , ' is_import asc, @id desc ' );
            $cl->SetRankingMode( SPH_RANK_PROXIMITY );
            $offset = ( $page - 1 ) * $pagesize;
            $cl->SetLimits( $offset , $pagesize );
            $res = $cl->Query( $keyword , env( 'SPHINX_INDEX_GOODS' ) );
            $goodsIdAry = $goodsList = [];
            if( isset( $res[ 'matches' ] ) && $res[ 'matches' ] ) {
                foreach( $res[ 'matches' ] as $k => $v ) {
                    $goodsIdAry[] = $v[ 'id' ];
                }
                if( $goodsIdAry ) {
                    $goodsList = $goodsObj->getGoodsMatchsList( $goodsIdAry );
                    if( $goodsList ) {
                        foreach( $goodsList as $k => $v ) {
                            if( $v->original_goods_id == $v->original_product_id && !$v->is_import ) {
                                $goodsList[ $k ]->master = 1;
                                $hasMaster = 1;
                            } else {
                                $goodsList[ $k ]->master = 0;
                            }
                            //$goodsList[$k]->shop_price = $this->_getCNY($v,$commonData['currencyHash']);
                        }
                    }
                }
                $searchd = 1;
            }
        } else {
            $goodsList = $res = $where = [];
            if( $brandId ) {
                $where[ 'brand_id' ] = $brandId;
            }
            if( $cate3Id ) {
                $where[ 'category_id' ] = $cate3Id;
            }
            if( $websiteAry ) {
                $where[ 'orignal_website_id' ] = $websiteAry;
            }
            if( $isMaster === '0' ) {
                $where[ 'isMaster' ] = 0;
            } else if( $isMaster === '1' ) {
                $where[ 'isMaster' ] = 1;
            }
            if( $where ) {
                $searchd = 1;
                $where['limit'] = $pagesize;
                $where['offset'] = ($page - 1) * $pagesize;
                $goodsList = $goodsObj->getGoodsMatchsListByWhere($where);
                if($goodsList) {
                    $goodsCount = $goodsObj->getGoodsMatchsCountByWhere($where);
                    if($goodsCount) {
                        $res['total_found'] = $goodsCount[0]->countNum;
                    } else {
                        $res[ 'total_found' ] = 0;
                    }
                    foreach( $goodsList as $k => $v ) {
                        if( $v->original_goods_id == $v->original_product_id && !$v->is_import ) {
                            $goodsList[ $k ]->master = 1;
                            $hasMaster = 1;
                        } else {
                            $goodsList[ $k ]->master = 0;
                        }
                        ////$goodsList[$k]->shop_price = $this->_getCNY($v,$commonData['currencyHash']);
                    }
                }
            }
        }
        if( $searchd ) {
            $goodsMatchParams = [];
            if( $websiteAry ) {
                $goodsMatchParams[ 'websiteAry' ] = $websiteAry;
            }
            if( $brandId ) {
                $goodsMatchParams[ 'brandId' ] = $brandId;
            }
            if( $cate1Id ) {
                $goodsMatchParams[ 'cate1Id' ] = $cate1Id;
            }
            if( $cate2Id ) {
                $goodsMatchParams[ 'cate2Id' ] = $cate2Id;
            }
            if( $cate3Id ) {
                $goodsMatchParams[ 'cate3Id' ] = $cate3Id;
            }
            if( $keyword ) {
                $goodsMatchParams[ 'keyword' ] = $keyword;
            }
            if( $pagesize ) {
                $goodsMatchParams[ 'pagesize' ] = $pagesize;
            }
            if( $isMaster != 2 ) {
                $goodsMatchParams[ 'isMaster' ] = $isMaster;
            }
            if( $goodsMatchParams ) {
                $request->session()->put( 'goodsMatchParams' , json_encode( $goodsMatchParams ) );
            }
        }
        $commonData[ 'goodsList' ] = $goodsList;
        $commonData[ 'keyword' ] = $keyword;
        $commonData[ 'websiteAry' ] = $websiteAry;
        $commonData[ 'mode' ] = $mode;
        $commonData[ 'res' ] = $res;
        $commonData[ 'brandId' ] = $brandId;
        $commonData[ 'cate1Id' ] = $cate1Id;
        $commonData[ 'cate2Id' ] = $cate2Id;
        $commonData[ 'cate3Id' ] = $cate3Id;
        $commonData[ 'pagesize' ] = $pagesize;
        $matchCount = $goodsObj->getGoodsMatchsCount();
        $commonData[ 'goodsMatchCount' ] = $matchCount;
        $commonData[ 'isMaster' ] = $isMaster;
        if( $ajax ) {
            if( $goodsList ) {
                return view( '/admin/product/goods_match_ajax' , $commonData );
            } else {
                return '';
            }
        }
        return view( '/admin/product/goods_match' , $commonData );
    }

    public function productsMatchDo( Request $request )
    {
        $goodsIdAry = $request->input( 'matchId' );
        $goodsObj = new Goods;
        $goodsList = $goodsObj->getProductsMatchsList( $goodsIdAry );
        $hasMaster = 0;
        $commonData = $this->_commonData();
        $commonData[ 'goodsList' ] = $goodsList;
        return view( '/admin/product/products_match_do' , $commonData );
    }

    public function productsMatchSave( Request $request )
    {
        $goodsIdAry = $request->input( 'goodsId' );
        $goodsObj = new Goods;
        $productId = $request->input( 'productId' );
        $goodsList = $goodsObj->getProductsMatchsList( $goodsIdAry );
        $updateProductIdAry = $updateGoodsIdAry = [];
        $oldProductId = 0;
        foreach( $goodsList as $k => $v ) {
            if( $productId != $v->pid ) {
                $updateProductIdAry[] = $v->pid;
            }
            $updateGoodsIdAry[] = $v->id;
            $oldProductId = $v->product_id;
        }
        $productObj = new Product;
        $priceObj = new Price;
        if( $updateProductIdAry && $updateGoodsIdAry && $productId ) {
            if( $productObj->doMatch( $updateProductIdAry , $productId , $updateGoodsIdAry ) ) {
                //设置新主体最低价
                $priceObj->setBestPrice( $productId , $goodsObj );
                //设置新主体价格标签
                $productObj->setPriceFlagByProductId( $productId );
                //设置旧主体最低价
                $priceObj->setBestPrice( $oldProductId , $goodsObj );
                //设置旧主体价格标签
                $productObj->setPriceFlagByProductId( $oldProductId );
                return [ 'code' => 1 , 'productId' => $productId ];
            } else {
                return [ 'code' => 2 , 'msg' => '合并失败，请重试' ];
            }
        } else {
            return [ 'code' => 2 , 'msg' => '请选择主体' ];
        }
    }

    public function goodsMatchDo( Request $request )
    {
        $goodsIdAry = $request->input( 'matchId' );
        $goodsObj = new Goods;
        $goodsList = $goodsObj->getGoodsMatchsList( $goodsIdAry );
        $hasMaster = 0;
        $commonData = $this->_commonData();
        foreach( $goodsList as $k => $v ) {
            if( $v->original_goods_id == $v->original_product_id && !$v->is_import ) {
                $goodsList[ $k ]->master = 1;
                $hasMaster = 1;
            } else {
                $goodsList[ $k ]->master = 0;
            }
            //$goodsList[$k]->shop_price = $this->_getCNY($v,$commonData['currencyHash']);
        }

        $commonData[ 'goodsList' ] = $goodsList;
        $commonData[ 'hasMaster' ] = $hasMaster;
        return view( '/admin/product/goods_match_do' , $commonData );
    }

    public function goodsMatchSave( Request $request )
    {
        $goodsIdAry = $request->input( 'goodsId' );
        $goodsObj = new Goods;
        $productId = $request->input( 'productId' );
        $goodsList = $goodsObj->getGoodsMatchsList( $goodsIdAry );
        $updateProductIdAry = $updateGoodsIdAry = [];
        foreach( $goodsList as $k => $v ) {
            if( $productId != $v->product_id ) {
                $updateProductIdAry[] = $v->product_id;
                if( !$v->is_import ) {
                    $subGoodsList = $goodsObj->getGoodsIdByProductId( $v->product_id );
                    if( $subGoodsList ) {
                        foreach( $subGoodsList as $m => $n ) {
                            $updateGoodsIdAry[] = $n->id;
                        }
                    }
                }
            }
            $updateGoodsIdAry[] = $v->id;
        }
        $productObj = new Product;
        $priceObj = new Price;
        if( $updateProductIdAry && $updateGoodsIdAry && $productId ) {
            if( $productObj->doMatch( $updateProductIdAry , $productId , $updateGoodsIdAry ) ) {
                //设置最低价
                $priceObj->setBestPrice( $productId , $goodsObj );
                //设置价格标签
                $productObj->setPriceFlagByProductId( $productId );
                return [ 'code' => 1 , 'productId' => $productId ];
            } else {
                return [ 'code' => 2 , 'msg' => '合并失败，请重试' ];
            }
        } else {
            return [ 'code' => 2 , 'msg' => '请选择主体' ];
        }
    }

    public function batchSetCate( Request $request )
    {
        $cateId = $request->input( 'matchcate' );
        $goodsIdAry = $request->input( 'matchId' );
        $goodsObj = new Goods;
        $CategoryChangeLogObj = new CategoryChangeLog;
        $goodsList = $goodsObj->getGoodsMatchsList( $goodsIdAry );
        $productIdAry = [];
        foreach( $goodsList as $k => $v ) {
            $productIdAry[] = $v->product_id;
            if( $v->original_category_name ) {
                if( !$CategoryChangeLogObj->checkExistsByWhere( [ 'original_category_name' => $v->original_category_name , 'category_id' => $cateId ] ) ) {
                    CategoryChangeLog::insert( [ 'original_category_name' => $v->original_category_name , 'category_id' => $cateId ] );
                }
            }
        }
        if( $cateId && $productIdAry ) {
            $productObj = new Product;
            $categoryObj = new Category;
            $rootCategoryId = $categoryObj->getRootCategoryIdByThridCategoryId( $cateId );
            //if($productObj->batchSetCategory($productIdAry,$cateId)) {
            if( $productObj->batchSetCategoryAndRootCategory( $productIdAry , $cateId , $rootCategoryId ) ) {
                return [ 'code' => 1 , 'msg' => '设置成功' ];
            } else {
                return [ 'code' => 2 , 'msg' => '设置失败，请检查' ];
            }
        }
    }

    public function batchSetCateByProductId( Request $request )
    {
        $cateId = $request->input( 'category_id' );
        $productId = $request->input( 'product_id' );
        $goodsObj = new Goods;
        $CategoryChangeLogObj = new CategoryChangeLog;
        $goodsList = $goodsObj->getGoodsMatchsListByProductId( $productId );
        $productIdAry = [];
        //print_r($goodsList);exit;
        foreach( $goodsList as $k => $v ) {
            $productIdAry[] = $v->pid;
            if( $v->original_category_name ) {
                if( !$CategoryChangeLogObj->checkExistsByWhere( [ 'original_category_name' => $v->original_category_name , 'category_id' => $cateId ] ) ) {
                    CategoryChangeLog::insert( [ 'original_category_name' => $v->original_category_name , 'category_id' => $cateId ] );
                }
            }
        }
        if( $cateId && $productIdAry ) {
            $productObj = new Product;
            $categoryObj = new Category;
            $rootCategoryId = $categoryObj->getRootCategoryIdByThridCategoryId( $cateId );
            //if($productObj->batchSetCategory($productIdAry,$cateId)) {
            if( $productObj->batchSetCategoryAndRootCategory( $productIdAry , $cateId , $rootCategoryId ) ) {
                return [ 'code' => 1 , 'msg' => '设置成功' ];
            } else {
                return [ 'code' => 2 , 'msg' => '设置失败，请检查' ];
            }
        }
    }

    public function batchSetKeywordByProductId( Request $request )
    {
        $keyword = $request->input( 'keyword' );
        if( !$keyword ) {
            return [ 'code' => 2 , 'msg' => '关键词不能为空' ];
        }
        $productId = $request->input( 'product_id' );
        if( !$productId ) {
            return [ 'code' => 2 , 'msg' => '商品id不能为空' ];
        }
        $keywordAry = preg_split( '/(，|,)+/' , $keyword );
        $tmp = [];
        foreach( $keywordAry as $k => $v ) {
            $v = trim( $v );
            if( $v ) {
                $tmp[] = $v;
            }
        }
        if( !$tmp ) {
            return [ 'code' => 2 , 'msg' => '关键词不能为空' ];
        }
        $keywordAry = $tmp;
        $goodsObj = new Goods;
        $goodsList = $goodsObj->getGoodsMatchsListByProductId( $productId );
        if( !$goodsList ) {
            return [ 'code' => 2 , 'msg' => '商品不存在' ];
        }
        $keywordIdTmp = [];
        $now = date( 'Y-m-d H:i:s' );
        if( $keywordAry && $goodsList ) {
            $keywordObj = new Keyword;
            $productObj = new Product;
            foreach( $goodsList as $k => $v ) {
                KeywordGoods::where( [ 'goods_id' => $v->id , 'is_custom' => 1 ] )->update( [ 'is_custom' => 0 ] );
                foreach( $keywordAry as $keyword ) {
                    if( isset( $keywordIdTmp[ $keyword ] ) ) {
                        $keywordId = $keywordIdTmp[ $keyword ];
                    } else {
                        $keywordId = $keywordObj->getKeywordId( $keyword );
                        $keywordIdTmp[ $keyword ] = $keywordId;
                    }
                    $keywordGoodsInfo = KeywordGoods::where( [ 'goods_id' => $v->id , 'keyword_id' => $keywordId ] )->first();
                    if( !$keywordGoodsInfo ) {
                        KeywordGoods::insertGetId( [ 'goods_id' => $v->id , 'keyword_id' => $keywordId , 'is_custom' => 1 , 'website_id' => $v->orignal_website_id , 'created_at' => $now ] );
                    } else {
                        if( !$keywordGoodsInfo->is_custom ) {
                            KeywordGoods::where( [ 'id' => $keywordGoodsInfo->id ] )->update( [ 'is_custom' => 1 ] );
                        }
                    }
                }
            }
            return [ 'code' => 1 , 'msg' => '设置成功' ];
        }
        return [ 'code' => 2 , 'msg' => '设置失败，请检查' ];
    }

    public function batchSetSpecsByProductId( Request $request )
    {
        $specs = $request->input( 'specs' );
        if( !$specs ) {
            return [ 'code' => 2 , 'msg' => '规格不能为空' ];
        }
        $productId = $request->input( 'product_id' );
        if( !$productId ) {
            return [ 'code' => 2 , 'msg' => '商品id不能为空' ];
        }
        $specsAry = preg_split( '/(，|,)+/' , $specs );
        $tmp = [];
        foreach( $specsAry as $k => $v ) {
            $v = trim( $v );
            if( $v ) {
                $tmp[] = $v;
            }
        }
        if( !$tmp ) {
            return [ 'code' => 2 , 'msg' => '规格不能为空' ];
        }
        $specsAry = $tmp;
        $goodsObj = new Goods;
        $goodsList = $goodsObj->getGoodsMatchsListByProductId( $productId );
        if( !$goodsList ) {
            return [ 'code' => 2 , 'msg' => '商品不存在' ];
        }
        $now = date( 'Y-m-d H:i:s' );
        $specsIdTmp = [];
        if( $specsAry && $goodsList ) {
            $specsObj = new Specs;
            $goodsSpecsObj = new GoodsSpecs;
            foreach( $goodsList as $k => $v ) {
                GoodsSpecs::where( [ 'goods_id' => $v->id , 'is_custom' => 1 ] )->update( [ 'is_custom' => 0 ] );
                foreach( $specsAry as $specs ) {
                    if( isset( $specsIdTmp[ $specs ] ) ) {
                        $specsId = $specsIdTmp[ $specs ];
                    } else {
                        $specsId = $specsObj->getSpecsId( $specs );
                        $specsIdTmp[ $specs ] = $specsId;
                    }

                    $goodsSpecsInfo = GoodsSpecs::where( [ 'goods_id' => $v->id , 'spec_id' => $specsId ] )->first();
                    if( !$goodsSpecsInfo ) {
                        GoodsSpecs::insertGetId( [ 'goods_id' => $v->id , 'spec_id' => $specsId , 'is_custom' => 1 , 'orignal_website_id' => $v->orignal_website_id , 'created_at' => $now ] );
                    } else {
                        if( !$goodsSpecsInfo->is_custom ) {
                            GoodsSpecs::where( [ 'id' => $goodsSpecsInfo->id ] )->update( [ 'is_custom' => 1 ] );
                        }
                    }
                }
            }
            return [ 'code' => 1 , 'msg' => '设置成功' ];
        }
        return [ 'code' => 2 , 'msg' => '设置失败，请检查' ];
    }

    //匹配同一件商品并做价格的逻辑处理
    public function match( Request $request )
    {

        $param[ 'product_id' ] = $request->route( "product" );
        $attribute_name_id = $request->route( "attribute_name_id" );
        $attribute_value_id = $request->route( "attribute_value_id" );


        //根据spu的id查出商品的基本信息
        $product_data = DB::table( 'fb_product' )->where( 'id' , $param[ 'product_id' ] )->first();

        if( $product_data ) {
            $product_data = get_object_vars( $product_data );
            $product_data[ 'product_keyword' ] = explode( ',' , $product_data[ 'product_keywords' ] );
        }
        //        dd($product_data);
        //查询所有的网站
        $website_data = DB::table( 'fb_website' )->where( 'website_status' , 1 )->get()->map( function( $value ) {
            return (array) $value;
        } )->toArray();

        if( $request->route( "attribute_name_id" ) && $request->route( "attribute_value_id" ) ) {

            $param[ 'attribute_name_id' ] = $request->route( "attribute_name_id" );

            $param[ 'attribute_value_id' ] = $request->route( "attribute_value_id" );

            $fb_attribute_value = DB::table( 'fb_attribute_value_enum' )->where( 'id' , $param[ 'attribute_value_id' ] )->first();

            $where_only = $this->getWhere( $product_data[ 'product_keywords' ] , $fb_attribute_value->attribute_value );
            //加上规格进行的唯一匹配
            $goods_data_one = $this->findGood( $website_data , $product_data[ 'brand_id' ] , $where_only );

            $where_two = $this->getWhere( $product_data[ 'product_keywords' ] );

            $goods_data_two = $this->findGood( $website_data , $product_data[ 'brand_id' ] , $where_two );

            $goods_data_two = $this->cleanData( $goods_data_one , $goods_data_two );

            //            dd($goods_data_two);

        } else {

            $goods_data_one = '';

            $fb_attribute_value = '';

            $where_two = $this->getWhere( $product_data[ 'product_keywords' ] );

            $goods_data_two = $this->findGood( $website_data , $product_data[ 'brand_id' ] , $where_two );

            $goods_data_two = $this->cleanData( $goods_data_one , $goods_data_two );


        }

        //根据搜索关键字和商品品牌匹配相同的商品
        return view( '/admin/product/match' , compact( 'website_data' , 'product_data' , 'fb_attribute_value' , 'goods_data_one' , 'goods_data_two' , 'attribute_value_id' , 'attribute_name_id' ) );
    }

    //匹配相同商品
    public function findGood( $website_data , $brand_id , $where )
    {
        $goods_data = [];
        //    dd($brand_id);
        $brand_id = intval( $brand_id );
        foreach( $website_data as $key => $val ) {
            $goods_data[] = DB::connection( 'mongodb' )
                ->collection( $val[ 'website_abbreviation' ] )
                ->where( [ 'brandID' => $brand_id , 'good_from' => $val[ 'website_abbreviation' ] , 'is_onsell' => 1 ] )
                ->where( $where )
                ->get()
                ->toArray();
        }
        //        dd($goods_data);
        $goods_data = array_filter( $goods_data );
        //        dd($goods_data);
        foreach( $goods_data as $g_k => $g_v ) {
            foreach( $g_v as $gg_k => $gg_v ) {
                $website = DB::table( 'fb_website' )->where( 'website_abbreviation' , $gg_v[ 'good_from' ] )->first();
                $goods_data[ $g_k ][ $gg_k ][ 'website_name' ] = $website->website_name;
                $goods_data[ $g_k ][ $gg_k ][ 'website_id' ] = $website->id;
                if( isset( $gg_v[ 'root_category_name' ] ) && isset( $gg_v[ 'category_name' ] ) && isset( $gg_v[ 'sub_category_name' ] ) ) {

                    $goods_data[ $g_k ][ $gg_k ][ 'category' ] = $gg_v[ 'root_category_name' ] . ',' . $gg_v[ 'category_name' ] . ',' . $gg_v[ 'sub_category_name' ];

                } else {

                    $goods_data[ $g_k ][ $gg_k ][ 'category' ] = '';
                }
                if( isset( $gg_v[ 'quantity' ] ) ) {

                    $goods_data[ $g_k ][ $gg_k ][ 'stock_number' ] = $gg_v[ 'quantity' ];

                } else {

                    $goods_data[ $g_k ][ $gg_k ][ 'stock_number' ] = 100;
                }
                $price = $this->getPrice( $gg_v[ 'add_to_field' ] , $gg_v[ 'good_from' ] );

                $goods_data[ $g_k ][ $gg_k ][ 'market_price' ] = $price[ 'market_price' ];

                $goods_data[ $g_k ][ $gg_k ][ 'shop_price' ] = $price[ 'shop_price' ];
            }
        }
        //        dd($goods_data);

        return $goods_data;
    }

    //拼接唯一where条件
    public function getWhere( $data , $fb_attribute_value = "" )
    {

        $where_found = explode( ',' , $data );

        $where = [];

        foreach( $where_found as $k => $v ) {

            $where[] = [ 'title' , 'like' , '%' . $v . '%' ];
        }

        if( $fb_attribute_value ) {

            if( !in_array( $fb_attribute_value , $where_found ) ) {

                $where[] = [ 'title' , 'like' , '%' . $fb_attribute_value . '%' ];

            }

        }

        return $where;
    }

    /**
     * @param $array1
     * @param $array2
     *
     * @return mixed
     * 处理模糊查询的数据
     */
    public function cleanData( $array1 , $array2 )
    {
        foreach( $array1 as $k1 => $v1 ) {
            foreach( $v1 as $kk1 => $vv1 ) {
                foreach( $array2 as $k2 => $v2 ) {
                    foreach( $v2 as $kk2 => $vv2 ) {
                        if( $vv1[ 'good_url' ] == $vv2[ 'good_url' ] ) {
                            unset( $array2[ $k2 ] );
                        }
                    }
                }
            }
        }
        return $array2;
    }

    /**
     * @param $data 涉及价格的所有信息
     * @param $website_abbreviation 网站的简写
     *
     * @return array $price['market_price'] 商品的市场价格(原价）
     * @return array $price['shop_price'] 商品的售价(现价)
     */
    public function getPrice( $data , $website_abbreviation )
    {

        //        dd($data);
        //取出商品最新的价格
        $result = array_pop( $data );
        //根据网站关键字查出网站上品属于类型
        //        dd($website_abbreviation);
        $website_type = DB::table( 'fb_website' )->where( [ 'website_abbreviation' => $website_abbreviation , 'website_status' => 1 ] )->first( [ 'pay_way' ] );
        $price = [];
        //国内直邮
        if( $website_type->pay_way == 1 ) {      //国内直邮
            //商品的价格=税费(国内商品是包含税费的)+邮费
            if( $result[ 'postage_price' ] ) {
                $postage_price = intval( $result[ 'postage_price' ] );
            } else {
                $postage_price = 0;
            }

            //判断邮费
            if( $result[ 'is_postage' ] == 1 ) {
                $price[ 'market_price' ] = intval( $result[ 'original_cost' ] );
                $price[ 'shop_price' ] = intval( $result[ 'price_text' ] );
            } else {
                $price[ 'market_price' ] = intval( $result[ 'original_cost' ] );
                $price[ 'shop_price' ] = intval( $result[ 'price_text' ] ) + $postage_price;
            }
        } elseif( $website_type->pay_way == 2 ) {    //海外直邮
            //商品的运费和税费是在一起的
            $total = intval( $result[ 'cross_border_tax' ] );

            //商品的价格
            $price[ 'market_price' ] = intval( $result[ 'price_text' ] );
            $price[ 'shop_price' ] = intval( $result[ 'price_text' ] ) + $total;

        } elseif( $website_type->pay_way == 3 ) {  //海淘直邮
            //计算税费
            if( $result[ 'is_import_fee_in' ] == 2 && $result[ 'import_fee' ] == "-1" ) {
                //走海关抽检后台
                $price_import = 0;

            } else {

                $price_import = intval( $result[ 'import_fee' ] );
            }

            //计算邮费
            if( $result[ 'is_postage' ] == 2 && $result[ 'postage_price' ] == "-1" ) {

                $postage_price = 0; //转运费 后台

            } else {

                $postage_price = intval( $result[ 'postage_price' ] );
            }

            //商品的价格
            $price[ 'market_price' ] = intval( $result[ 'no_import_fee_in_price' ] );

            $price[ 'shop_price' ] = intval( $result[ 'no_import_fee_in_price' ] ) + $price_import + $postage_price;


        } elseif( $website_type->pay_way == 4 ) {     //海淘转运
            //计算税费  0
            if( $result[ 'is_import_fee_in' ] == 2 && $result[ 'import_fee' ] == "-1" ) {
                //走海关抽检后台
                $price_import = 0;

            } else {

                $price_import = intval( $result[ 'import_fee' ] );
            }


            //计算邮费  0 转运费后台出 目前为0

            if( $result[ 'is_postage' ] == 2 && $result[ 'postage_price' ] == "-1" ) {

                $no_import_fee_in_price = 0; //转运费 后台

            } else {

                $no_import_fee_in_price = intval( $result[ 'postage_price' ] );
            }

            //商品的价格

            $price[ 'market_price' ] = intval( $result[ 'price_text' ] );
            $price[ 'shop_price' ] = intval( $result[ 'price_text' ] ) + $price_import + $no_import_fee_in_price;


        } elseif( $website_type->pay_way == 5 ) {   //免税店
            //计算税费 先判断是否含当地税费

            if( $result[ 'is_local_tax_in' ] == 2 && $result[ 'tax_refund_price' ] == "-1" ) {

                $price[ 'market_price' ] = intval( $result[ 'duty_free_price' ] );
                $price[ 'shop_price' ] = intval( $result[ 'duty_free_price' ] );

            } else {

                $price[ 'market_price' ] = intval( $result[ 'local_tax_in_price' ] );

                $price[ 'shop_price' ] = intval( $result[ 'local_tax_in_price' ] ) - intval( $result[ 'tax_refund_price' ] );
            }

        }
        //换算汇率
        $prices = $this->exchangeRate( $price , $result[ 'currency' ] );

        //        dd($prices);

        return $prices;
    }

    /**
     * @param $price  商品的价格
     * @param $price_type 货币的类型
     *
     * @return mixed
     */
    public function exchangeRate( $price , $price_type )
    {

        $currency_rate = DB::table( 'fb_currency' )->where( [ 'currency_unit' => $price_type , 'currency_status' => 1 ] )->first( [ 'currency_rate' ] );

        $price[ 'market_price' ] = ( $price[ 'market_price' ] / 100 ) * $currency_rate->currency_rate;

        $price[ 'shop_price' ] = ( $price[ 'shop_price' ] / 100 ) * $currency_rate->currency_rate;

        return $price;

    }

    /*
     * 修改关键字
     */
    public function saveWords( Request $request , Product $product )
    {

        $data = \request()->all();

        $id = $request->post( 'product_id' );

        $fb_attribute_value = $request->post( 'fb_attribute_value' );

        $brand_id = $request->post( 'brand_id' );

        if( $data[ 'search' ] ) {
            $data[ 'search' ] = implode( ',' , $data[ 'search' ] );
        }

        $res = Product::where( 'id' , $id )->first();

        $res->product_keywords = $data[ 'search' ];

        $where_one = $this->getWhere( $data[ 'search' ] , $fb_attribute_value );

        $where_two = $this->getWhere( $data[ 'search' ] );

        if( $res->save() ) {

            $website_data = DB::table( 'fb_website' )->where( 'website_status' , 1 )->get()->map( function( $value ) {
                return (array) $value;
            } )->toArray();

            //查询mysql数据剔除已存的网站
            $goods_data = DB::table( 'fb_goods' )->where( 'product_id' , $id )->get()->map( function( $value ) {
                return (array) $value;
            } )->toArray();


            if( $goods_data ) {

                foreach( $goods_data as $go_k => $go_v ) {

                    foreach( $website_data as $k => $v ) {

                        if( $go_v[ 'orignal_website' ] == $v[ 'website_abbreviation' ] ) {

                            unset( $website_data[ $k ] );
                        }
                    }

                }
            }

            $good_data_one = $this->findGood( $website_data , $brand_id , $where_one );

            $good_data_two = $this->findGood( $website_data , $brand_id , $where_two );

            $good_data_two = $this->cleanData( $good_data_one , $good_data_two );

            //            dd($good_data_two);

            $str_one = "";

            if( !empty( $good_data_one ) ) {
                foreach( $good_data_one as $kk => $vv ) {
                    foreach( $vv as $k => $v ) {
                        $str_one .= " 
                    <div class=\"layui-form-item\" >
                    <div class=\"layui-input-inline\" style=\"width: 20px;\" name=\"sousuo\">
                         <input type=\"checkbox\" name=\"sd\" value=\"11111\" lay-filter=\"choose\" website_name='$v[website_name]' good_url='$v[good_url]' good_name='$v[title]' price_text='$v[price_text]' good_id='$v[good_id]'>
                    </div>
                    <div class=\"layui-input-inline\" style=\"width: 110px;\">
                        <input type=\"text\" placeholder=\"网站\" name=\"website_name[]\" class=\"layui-input website_name\" value='$v[website_name]' style=\"width: 100px;margin-left: 12px;\" />
                    </div>
                    <div class=\"layui-input-inline\" style=\"width: 445px;margin-left: 10px \">
                        <a href='$v[good_url].' target=\"_blank\" style=\"text-decoration:none\" >
                        <input type=\"text\" placeholder=\"商品名称\" name=\"good_name[]\"  class=\"layui-input\" value='$v[title]'/>
                        </a>
                    </div>
                    <div class=\"layui-input-inline\" style=\"width: 50px;margin-left: 10px\">
                        <input type=\"text\" placeholder=\"价格\" name=\"price_text[]\" class=\"layui-input\" value='$v[price_text]'/>
                    </div>
                    <input type=\"hidden\" class=\"layui-input box\" value='$v[good_id]'  name=\"orignal_website_id\" >
                    <input type=\"hidden\" class=\"layui-input box\" value='$v[market_price]'  name=\"market_price\" >
                    <input type=\"hidden\" class=\"layui-input box\" value='$v[shop_price]'  name=\"shop_price\" >
                    <input type=\"hidden\" class=\"layui-input box\" value='$v[stock_number]'  name=\"stock_number\" >
                    <input type=\"hidden\" class=\"layui-input box\" value='$v[is_onsell]'  name=\"goods_status\" >
                    <div class=\"layui-input-inline\" style=\"display:inline-block\">
                        <a num=\"1\" name=\"hulue\" style=\"display:inline-block;width: 60px;height: 38px;background:#009688;line-height: 38px;text-align: center;cursor: pointer;\">
                            ∅忽略
                        </a>
                    </div>
                    </div>
                    ";
                    }
                }
            } else {
                $str_one = "暂无唯一商品";
            }

            $str_two = "";
            if( !empty( $good_data_two ) ) {
                foreach( $good_data_two as $two_kk => $two_vv ) {
                    foreach( $two_vv as $two_k => $two_v ) {
                        $str_two .= "  
                <div class=\"layui-form-item\" >
                <div class=\"layui-input-inline\" style=\"width: 20px;\" name=\"sousuo\">
                    <input type=\"checkbox\" name=\"sd\" value=\"11111\" lay-filter=\"choose\" website_name='$two_v[website_name]' good_url='$two_v[good_url]' good_name='$two_v[title]' good_id='$two_v[good_id]'>
                </div>
                <div class=\"layui-input-inline\" style=\"width: 90px;margin-left: 12px;\">
                    <input type=\"text\" placeholder=\"网站\" name=\"website_name[]\" class=\"layui-input\" value='$two_v[website_name]'/>
                </div>
                <div class=\"layui-input-inline\" style=\"width: 600px;margin-left: 10px \">
                    <a href='.$two_v[good_url].' target=\"_blank\" style=\"text-decoration:none\" >
                        <input type=\"text\" placeholder=\"商品名称\" name=\"good_name[]\"  class=\"layui-input\" value='$two_v[title]'/>
                    </a>
                </div>
                <div class=\"layui-input-inline\" style=\"width: 66px;margin-left: 10px\">
                    <input type=\"text\" placeholder=\"价格\" name=\"price_text[]\" class=\"layui-input\" value='$two_v[price_text]' />
                </div>
                <input type=\"hidden\" class=\"layui-input box\" value='$two_v[good_id]'  name=\"orignal_website_id\" >
                <input type=\"hidden\" class=\"layui-input box\" value='$two_v[market_price]'  name=\"market_price\" >
                <input type=\"hidden\" class=\"layui-input box\" value='$two_v[shop_price]'  name=\"shop_price\" >
                <input type=\"hidden\" class=\"layui-input box\" value='$two_v[stock_number]'  name=\"stock_number\" >
                <input type=\"hidden\" class=\"layui-input box\" value='$two_v[is_onsell]'  name=\"goods_status\" >

                <div class=\"layui-input-inline\">
                    <a num=\"1\" name=\"hulue\" style=\"display:inline-block;width: 60px;height: 38px;background:
                #009688;
                line-height: 38px;text-align: center;cursor: pointer;\">∅忽略</a>
                </div>
                </div>";
                    }
                }

            } else {
                $str_two .= "暂无模糊商品";
            }

            $result[ 'good_data_one' ] = $str_one;

            $result[ 'good_data_two' ] = $str_two;

            return [ 'code' => 1 , 'data' => $result ];

        } else {
            return [ 'code' => 2 , 'font' => '参数错误' ];
        }

        //修改数据库

    }

    public function keyWordSearch( Request $request )
    {
        $id = $request->post( 'product_id' );
        $goods_data = DB::table( 'fb_goods' )
            ->where( 'product_id' , $id )
            ->get()
            ->map( function( $value ) {
                return (array) $value;
            } )
            ->toArray();
        $website_data = DB::table( 'fb_website' )->where( 'website_status' , 1 )->get()->map( function( $value ) {
            return (array) $value;
        } )->toArray();
        if( $goods_data ) {
            foreach( $goods_data as $k => $v ) {
                foreach( $website_data as $web_k => $web_v ) {
                    if( $web_v[ 'website_abbreviation' ] == $v[ 'orignal_website' ] ) {
                        unset( $website_data[ $web_k ] );
                    }
                }
            }
        }
        $web_str = "<select name=\"website_name\" lay-filter=\"website_name\">";
        foreach( $website_data as $w_k => $w_v ) {
            $web_str .= "<option value='$w_v[website_abbreviation]' class=\"website_abbreviation\">$w_v[website_name]</option>";
        }
        $web_str .= "</select>";
        return [ 'code' => 1 , 'data' => $web_str ];
    }

    public function selectId( Request $request )
    {

        $website_name = $request->post( "webname" );

        $product_id = $request->post( "ma_id" );

        //根据传过来的信息提前带mysql查下有无这条数据
        $where = [ 'orignal_website' => $website_name ,
            'product_id' => $product_id
        ];
        $goods_data = DB::table( 'fb_goods' )->where( $where )->first();

        if( $goods_data ) {

            return [ 'code' => 2 , 'font' => '该商品已添加' ];

        } else {
            $res = DB::connection( 'mongodb' )
                ->collection( $website_name )
                ->where( [ "good_id" => $product_id , "good_from" => $website_name , "is_onsell" => 1 ] )
                ->first();

            //处理数据
            if( !$res ) {
                return [ 'font' => '请确认你的数据是否正确' , 'code' => 2 ];
            } else {
                //商品价格
                $result = array_pop( $res[ 'add_to_field' ] );

                $website_data = DB::table( 'fb_website' )->where( 'website_abbreviation' , $website_name )->first();

                $res[ 'shop_price' ] = $result[ 'price_text' ];
                $res[ 'website_name' ] = $website_data->website_name;
                if( isset( $res[ 'quantity' ] ) ) {

                    $res[ 'stock_number' ] = $res[ 'quantity' ];

                } else {

                    $res[ 'stock_number' ] = 100;
                }

                $res[ 'market_price' ] = $result[ 'original_cost' ];
            }
            //        dd($res);
            $str = "";
            $str .= "   <div class=\"layui-input-inline\" style=\"width: 20px;\" name=\"sousuo\">
                    <input type=\"checkbox\" name=\"sd\"  lay-filter=\"choose\"
                           website_name='$res[website_name]' original_goods_url='$res[good_url]'
                           goods_name='$res[title]' market_price='$res[market_price]'
                           original_goods_id='$res[good_id]' shop_price='$res[shop_price]'
                           stock_number='$res[stock_number]' goods_status='$res[is_onsell]'
                           orignal_website='$res[good_from]' />
                </div>
                <div class=\"layui-input-inline\" style=\"width: 80px;margin-left: 12px;\">
                    <input type=\"text\" placeholder=\"网站\" name=\"website_name[]\" class=\"layui-input\"
                           value='$res[website_name]' />
                </div>
                <div class=\"layui-input-inline\" style=\"width: 600px;margin-left: 10px \">
                    <a href='$res[good_url]' target=\"_blank\" style=\"text-decoration:none\" >
                        <input type=\"text\" placeholder=\"商品名称\" name=\"good_name[]\"  class=\"layui-input\"
                               value='$res[title]'
                        />
                    </a>
                </div>
                <div class=\"layui-input-inline\" style=\"width: 66px;margin-left: 10px\">
                    <input type=\"text\" placeholder=\"价格\" name=\"price_text[]\" class=\"layui-input\"
                           value='$res[price_text]' />
                </div>
                <input type=\"hidden\" class=\"layui-input box\" value='$res[good_id]'  name=\"orignal_website_id\" >
                <input type=\"hidden\" class=\"layui-input box\" value='$res[market_price]'  name=\"market_price\" >
                <input type=\"hidden\" class=\"layui-input box\" value='$res[shop_price]'  name=\"shop_price\" >
                <input type=\"hidden\" class=\"layui-input box\" value='$res[stock_number]'  name=\"stock_number\" >
                <input type=\"hidden\" class=\"layui-input box\" value='$res[is_onsell]'  name=\"goods_status\" >

                <div class=\"layui-input-inline\">
                    <a num=\"1\" name=\"hulue\" style=\"display:inline-block;width: 60px;height: 38px;background:
                #009688;
                line-height: 38px;text-align: center;cursor: pointer;\">∅忽略</a>
                </div>";
            return [ 'code' => 1 , 'data' => $str ];
        }
    }

    private function _getProductMaster( $product_id )
    {
        return DB::table( 'fb_product' )
            ->leftJoin( 'fb_goods' , 'fb_goods.original_goods_id' , '=' , 'fb_product.original_product_id' )
            ->leftJoin( 'fb_website' , 'fb_goods.orignal_website' , '=' , 'website_abbreviation' )
            ->leftJoin( 'fb_country' , 'fb_website.website_country' , '=' , 'fb_country.id' )
            ->leftJoin( 'fb_brand' , 'fb_product.brand_id' , '=' , 'fb_brand.id' )
            ->leftJoin( 'fb_category' , 'fb_product.category_id' , '=' , 'fb_category.id' )
            ->where( [ 'fb_product.id' => $product_id ] )
            ->first( [ 'is_postage' , 'fb_product.product_keywords' , 'product_weight' , 'delivery_from' , 'tax_free_zone' , 'country' , 'fb_goods.update_type' , 'shop_price' , 'is_onsell' , 'stock_number' , 'cross_border_tax' , 'postage_price' , 'tax_free_zone' , 'transport_city' , 'fb_category.parent_id as two_category_id' , 'price_updated_at' , 'promotion_info' , 'product_name' , 'product_status' , 'fb_goods.orignal_website' , 'fb_product.updated_at' , 'brand_id' , 'category_id as three_category_id' , 'goods_name' , 'good_specs' , 'fb_goods.id as goods_id' , 'fb_product.id' , 'product_image' , 'click_number' , 'market_price' , 'comment_count' , 'website_name' , 'brand_chinese_name as brand_name' , 'product_label' , 'fb_goods.is_import' , 'fb_goods.product_id' , 'fb_product.is_master' ] );
    }

    /*
     *商品修改
     */
    public function update( Request $request )
    {

        $product_id = $request->route( 'product' );

        $product = $this->_getProductMaster( $product_id );

        $product->product_image = $this->imageCompatible( $product->product_image );

        if( $product->two_category_id && $product->two_category_id != 0 ) {

            $one_category_id = DB::table( 'fb_category' )->where( [ 'id' => $product->two_category_id ] )->first( [ 'parent_id' ] );

            if( $one_category_id ) {

                $product->one_category_id = $one_category_id->parent_id;

                $category_two = DB::table( 'fb_category' )->where( 'parent_id' , $one_category_id->parent_id )->get();
            }

            $category_three = DB::table( 'fb_category' )->where( [ 'parent_id' => $product->two_category_id ] )->get();

        } else {

            $product->two_category_id = null;

            $product->three_category_id = null;
        }

        $category_one = DB::table( 'fb_category' )->where( 'category_level' , 1 )->get();

        $product_label = explode( ',' , $product->product_label );

        if( $product->good_specs ) {

            $specs = $product->good_specs;

            if( $specs ) {

                $product->good_specs = json_decode( $product->good_specs , true );


            } else {

                $product->good_specs = "";

            }
        }

        $promotion_info = json_decode( $product->promotion_info , true );

        if( $promotion_info && isset( $promotion_info[ 'price_tags' ] ) && $promotion_info[ 'price_tags' ] ) {

            foreach( $promotion_info[ 'price_tags' ] as $tion_k => $tion_v ) {

                if( isset( $promotion_info[ 'promotion_contents' ][ $tion_k ] ) ) {

                    $promotion_info[] = $promotion_info[ 'price_tags' ][ $tion_k ] . ':' . $promotion_info[ 'promotion_contents' ][ $tion_k ];

                } else {

                    $promotion_info[] = $promotion_info[ 'price_tags' ][ $tion_k ];
                }

            }

            unset( $promotion_info[ 'price_tags' ] );

            unset( $promotion_info[ 'promotion_contents' ] );

            $product->promotion_info = $promotion_info;

        } else {

            $product->promotion_info = '';

        }

        if( $product->is_postage === 2 ) {

            $product->shop_price = $product->shop_price + $product->postage_price;
        }

        if( $product->cross_border_tax == -1 ) {

            $product->cross_border_tax = 0;

        }


        //        dd($product);

        #查询属性值，属性

        //        $attribute_value_id=DB::table('fb_goods_attribute_mapping')
        //
        //            ->where('fb_goods_attribute_mapping.goods_id','=',$product->goods_id)
        //
        //            ->get(['attribute_id','attribute_value_id']);

        //$attribute_name=DB::table('fb_attribute_name_enum')->get();

        //$attribute_value=DB::table('fb_attribute_value_enum')->get();


        //        DB::connection()->enableQueryLog();#开启执行日志
        //        $product->goods_data=DB::table('fb_goods')
        //            ->leftJoin('fb_goods_attribute_mapping', 'fb_goods.id', '=', 'fb_goods_attribute_mapping.goods_id')
        //            ->leftJoin('fb_website', 'fb_goods.orignal_website', '=', 'fb_website.website_abbreviation')
        //            ->leftJoin('fb_attribute_name_enum','fb_attribute_name_enum.id','=','fb_goods_attribute_mapping.attribute_id')
        //            ->leftJoin('fb_attribute_value_enum','fb_attribute_value_enum.id','=','fb_goods_attribute_mapping.attribute_value_id')
        //            ->where(['fb_goods.is_deleted'=>1,'fb_goods.product_id'=>$product_id])
        //            ->get(['fb_goods.*','fb_website.website_name','fb_goods_attribute_mapping.goods_id','attribute_name','fb_goods_attribute_mapping.attribute_value_id','fb_goods_attribute_mapping.attribute_id','attribute_value']);
        //            ->get(['fb_goods.*','fb_website.website_name']);
        //        dd(DB::getQueryLog());

        //
        //        foreach ($product->goods_data as $pro_k=>$pro_v){
        //            $product->attribute_name=$pro_v->attribute_name;
        //            $product->attribute_value_id=$pro_v->attribute_value_id;
        //            $product->attribute_id=$pro_v->attribute_id;
        //            $product->attribute_value=$pro_v->attribute_value;
        //            $product->good_specs=json_decode($pro_v->good_specs,true);


        //            $time = time()-strtotime($pro_v->price_updated_at);
        //            $product->time=round($time/3600/24);
        //
        //            if ($pro_v->orignal_website =='kaola'){
        //                $product->market_price ='￥'. $pro_v->market_price;
        //                $product->goods_id=$pro_v->id;
        //            }
        //        }

        //        URL::previous();
        //        $url=url()->current();
        //        dd($url);
        //        $returnUrl = $request->getRequestUri();
        $returnUrl = url()->previous();
        #查询分类

        //        $this->getInfoPrice();
        $commonData = $this->_commonData();
        $priceTmp = $priceFlag = [];
        $goodsObj = new Goods;
        $goodsSpecsObj = new GoodsSpecs;
        $keywordGoodsObj = new KeywordGoods;
        if( !$product->is_master ) {
            $goodsList = $goodsObj->getGoodsMatchsListByGoodsId( $product->goods_id );
            $productMaster = $this->_getProductMaster( $product->product_id );
        } else {
            $goodsList = $goodsObj->getGoodsMatchsListByProductId( $product_id );
            $productMaster = $product;
            $goodsIdAry = [];
            foreach( $goodsList as $k => $v ) {
                $goodsIdAry[] = $v->id;
                if( $commonData[ 'websiteHash' ][ $v->orignal_website_id ][ 'pay_way' ] == 1 || $commonData[ 'websiteHash' ][ $v->orignal_website_id ][ 'pay_way' ] == 2 ) {
                    $priceTmp[ 'domestic' ][ $v->id ] = $v->shop_price;
                } else if( $commonData[ 'websiteHash' ][ $v->orignal_website_id ][ 'pay_way' ] == 3 || $commonData[ 'websiteHash' ][ $v->orignal_website_id ][ 'pay_way' ] == 4 ) {
                    $priceTmp[ 'abroad' ][ $v->id ] = $v->shop_price;
                } else if( $commonData[ 'websiteHash' ][ $v->orignal_website_id ][ 'pay_way' ] == 5 ) {
                    $priceTmp[ 'dutyfree' ][ $v->id ] = $v->shop_price;
                }
                $priceTmp[ 'all' ][ $v->id ] = $v->shop_price;
            }
            foreach( $priceTmp as $k => $v ) {
                $cur_price = current( $v );
                $cur_goods_id = key( $v );
                foreach( $v as $m => $n ) {
                    if( $n < $cur_price ) {
                        $cur_price = $n;
                        $cur_goods_id = $m;
                    }
                }
                if( $k == 'domestic' && !isset( $priceFlag[ $cur_goods_id ] ) ) {
                    $priceFlag[ $cur_goods_id ] = '国内最低价';
                }
                if( $k == 'abroad' && !isset( $priceFlag[ $cur_goods_id ] ) ) {
                    $priceFlag[ $cur_goods_id ] = '海淘最低价';
                }
                if( $k == 'dutyfree' && !isset( $priceFlag[ $cur_goods_id ] ) ) {
                    $priceFlag[ $cur_goods_id ] = '免税店最低价';
                }
                if( $k == 'all' ) {
                    $priceFlag[ $cur_goods_id ] = '全球最低价';
                }
            }
            $goodsSpecsList = $goodsSpecsObj->getGoodsSpecsListByGoodsId( $goodsIdAry );
            $goodsSpecsTmp = [];
            if( $goodsSpecsList ) {
                foreach( $goodsSpecsList as $k => $v ) {
                    if( $v->is_custom ) {
                        $goodsSpecsTmp[ 'custom' ][ $v->goods_id ][] = $v->spec_name;
                    } else {
                        $goodsSpecsTmp[ 'import' ][ $v->goods_id ][] = $v->spec_name;
                    }
                }
            }
            $keywordGoodsList = $keywordGoodsObj->getKeywordGoodsListByGoodsId( $goodsIdAry );
            $keywordGoodsTmp = [];
            if( $keywordGoodsList ) {
                foreach( $keywordGoodsList as $k => $v ) {
                    if( $v->is_custom ) {
                        $keywordGoodsTmp[ 'custom' ][ $v->goods_id ][] = $v->keyword_name;
                    } else {
                        $keywordGoodsTmp[ 'import' ][ $v->goods_id ][] = $v->keyword_name;
                    }
                }
            }
        }
        $hasMaster = 0;

        $title = $request->input( 'title' , '' );

        foreach( $goodsList as $k => $v ) {
            $specCustom = 0;
            if( isset( $priceFlag[ $v->id ] ) ) {
                $goodsList[ $k ]->price_flag = $priceFlag[ $v->id ];
            } else {
                $goodsList[ $k ]->price_flag = '';
            }
            if( isset( $goodsSpecsTmp[ 'custom' ][ $v->id ] ) ) {
                $specCustom = 1;
                $goodsList[ $k ]->good_specs = json_encode( $goodsSpecsTmp[ 'custom' ][ $v->id ] );
                if( $v->pid == $product->id && !$product->good_specs ) {
                    $product->good_specs = $goodsSpecsTmp[ 'custom' ][ $v->id ];
                }
            }
            if( isset( $goodsSpecsTmp[ 'import' ][ $v->id ] ) && !$specCustom ) {
                $goodsList[ $k ]->good_specs = json_encode( $goodsSpecsTmp[ 'import' ][ $v->id ] );
                if( $v->pid == $product->id && !$product->good_specs ) {
                    $product->good_specs = $goodsSpecsTmp[ 'import' ][ $v->id ];
                }
            }
            $keywordCustom = 0;
            if( isset( $keywordGoodsTmp[ 'custom' ][ $v->id ] ) ) {
                $keywordCustom = 1;
                $goodsList[ $k ]->product_keywords = implode( ',' , $keywordGoodsTmp[ 'custom' ][ $v->id ] );
                if( $v->pid == $product->id && !$product->product_keywords ) {
                    $product->product_keywords = $goodsList[ $k ]->product_keywords;
                }
            }
            if( isset( $keywordGoodsTmp[ 'import' ][ $v->id ] ) && !$keywordCustom ) {
                $goodsList[ $k ]->product_keywords = implode( ',' , $keywordGoodsTmp[ 'import' ][ $v->id ] );
                if( $v->pid == $product->id && !$product->product_keywords ) {
                    $product->product_keywords = $goodsList[ $k ]->product_keywords;
                }
            }
            if( $title ) {
                if( !preg_match( '/' . $title . '/' , $v->goods_name ) ) {
                    unset( $goodsList[ $k ] );
                }
            }
        }
        $tmp = [];
        /*foreach($goodsList as $k => $v) {
            $tmp[$v->orignal_website_id][] = $v;
        }
        print_r($tmp);*/
        $goodsCount = count( $goodsList );
        $brandHash = $commonData[ 'brandHash' ];
        $websiteHash = $commonData[ 'websiteHash' ];
        $matchMode = $commonData[ 'matchMode' ];
        $imageCdnUrl = $commonData[ 'imageCdnUrl' ];
        $tabName = $request->input( 'tab' );
        $categoryAry = $commonData[ 'categoryAry' ];
        if( !in_array( $tabName , [ 'base' , 'match' , 'price' ] ) ) {
            $tabName = 'base';
        }

        return view( '/admin/product/update' , compact( 'product' , 'category_one' , 'category_two' , 'category_three' , 'returnUrl' , 'product_label' , 'goodsList' , 'hasMaster' , 'brandHash' , 'websiteHash' , 'matchMode' , 'imageCdnUrl' , 'productMaster' , 'tabName' , 'title' , 'goodsCount' , 'categoryAry' ) );

    }

    public function delGoodsMatch( Request $request )
    {
        $goods_id = $request->input( 'goods_id' );
        $goodsObj = new Goods;
        $goodsList = $goodsObj->getGoodsMatchsListByGoodsId( $goods_id );
        $goods = isset( $goodsList[ 0 ] ) ?
            $goodsList[ 0 ]:
            '';
        if( $goods ) {
            $priceObj = new Price;
            $productObj = new Product;
            if( $productObj->delMatch( $goods->pid , $goods_id ) ) {
                //设置最低价
                $priceObj->setBestPrice( $goods->product_id , $goodsObj );
                //设置价格标签
                $productObj->setPriceFlagByProductId( $goods->product_id );
                return [ 'code' => 1 ];
            } else {
                return [ 'code' => 2 , '操作失败，请重试' ];
            }
        } else {
            return [ 'code' => 2 , 'msg' => '商品不存在' ];
        }
    }


    public function updatePrice( Request $request , Product $product )
    {

        $data = $request->post();

        if( $data[ 'country' ] == null ) {
            $data[ 'country' ] = "";
        }

        if( $data[ 'delivery_from' ] == null ) {
            $data[ 'delivery_from' ] = "";
        }

        if( $data[ 'tax_free_zone' ] == null ) {
            $data[ 'tax_free_zone' ] = "";
        }

        $id = $data[ 'goods_id' ];

        unset( $data[ '_token' ] , $data[ 'goods_id' ] , $data[ 'country' ] );
        $res = DB::table( 'fb_goods' )
            ->where( [ 'id' => $id ] )
            ->update( $data );

        return redirect()->back();

    }

    /*
     * 修改完成跳转地址
     */
    public function updateFinish( Request $request )
    {

        $url = $request->post( 'returnUrl' );

        return redirect( $url );
    }

    public function selectIdUp( Request $request )
    {

        $website_name = $request->post( "webname" );

        $product_id = $request->post( "ma_id" );

        //根据传过来的信息提前带mysql查下有无这条数据
        $where = [ 'orignal_website' => $website_name ,
            'product_id' => $product_id
        ];

        $goods_data = DB::table( 'fb_goods' )->where( $where )->first();
        if( $goods_data ) {

            return [ 'code' => 2 , 'font' => '该商品已添加' ];

        } else {
            $res = DB::connection( 'mongodb' )
                ->collection( $website_name )
                ->where( [ "good_id" => $product_id , "good_from" => $website_name , "is_onsell" => 1 ] )
                ->first();

            //处理数据
            if( !$res ) {
                return [ 'font' => '请确认你的数据是否正确' , 'code' => 2 ];
            } else {
                //商品价格
                $result = array_pop( $res[ 'add_to_field' ] );

                $website_data = DB::table( 'fb_website' )->where( 'website_abbreviation' , $website_name )->first();

                $res[ 'shop_price' ] = $result[ 'price_text' ];
                $res[ 'website_name' ] = $website_data->website_name;
                if( isset( $res[ 'quantity' ] ) ) {

                    $res[ 'stock_number' ] = $res[ 'quantity' ];

                } else {

                    $res[ 'stock_number' ] = 100;
                }

                $res[ 'market_price' ] = $result[ 'original_cost' ];
            }

            $str = "";
            $str .= " <div class=\"layui-input-inline\" style=\"width: 20px;\" name=\"sousuo\">
                    <input type=\"checkbox\" name=\"sd\"  lay-filter=\"choose\"
                           website_name='$res[website_name]' original_goods_url='$res[good_url]'
                           goods_name='$res[title]' market_price='$res[market_price]'
                           original_goods_id='$res[good_id]' shop_price='$res[shop_price]'
                           stock_number='$res[stock_number]' goods_status='$res[is_onsell]'
                           orignal_website='$res[good_from]' />
                </div>
                <div class=\"layui-input-inline\" style=\"width: 80px;margin-left: 12px;\">
                    <input type=\"text\" placeholder=\"网站\" name=\"website_name[]\" class=\"layui-input\"
                           value='$res[website_name]' />
                </div>
                <div class=\"layui-input-inline\" style=\"width: 600px;margin-left: 10px \">
                    <a href='$res[good_url]' target=\"_blank\" style=\"text-decoration:none\" >
                        <input type=\"text\" placeholder=\"商品名称\" name=\"good_name[]\"  class=\"layui-input\"
                               value='$res[title]'
                        />
                    </a>
                </div>
                <div class=\"layui-input-inline\" style=\"width: 66px;margin-left: 10px\">
                    <input type=\"text\" placeholder=\"价格\" name=\"price_text[]\" class=\"layui-input\"
                           value='$res[price_text]' />
                </div>
                <input type=\"hidden\" class=\"layui-input box\" value='$res[good_id]'  name=\"orignal_website_id\" >
                <input type=\"hidden\" class=\"layui-input box\" value='$res[market_price]'  name=\"market_price\" >
                <input type=\"hidden\" class=\"layui-input box\" value='$res[shop_price]'  name=\"shop_price\" >
                <input type=\"hidden\" class=\"layui-input box\" value='$res[stock_number]'  name=\"stock_number\" >
                <input type=\"hidden\" class=\"layui-input box\" value='$res[is_onsell]'  name=\"goods_status\" >

                <div class=\"layui-input-inline\">
                    <a num=\"1\" name=\"hulue\" style=\"display:inline-block;width: 60px;height: 38px;background:
                #009688;
                line-height: 38px;text-align: center;cursor: pointer;\">∅忽略</a>
                </div>";
            return [ 'code' => 1 , 'data' => $str ];
        }
    }

    /*
     * 执行商品基本信息修改
     */
    public function updateInfo( Request $request , Product $product )
    {
        $data = $request->post();
        //        dd($data);
        //验证
        $this->validate( $request , [
            'product_name' => 'required' ,
            //            'three_category_id'=>'required',
            'click_number' => 'required' ,
            'product_status' => 'required' ,
            'product_label' => 'required' ,
            'comment_count' => 'required' ,
        ] );

        $product_label = $request->post( 'product_label' );

        $product_label = implode( ',' , $product_label );

        #修改商品的基本信息
        $id = $request->post( 'id' );
        $res = Product::where( 'id' , $id )->first();
        $res->product_name = $request->post( 'product_name' );
        //        $res->category_id = $request->post('three_category_id');
        $res->click_number = $request->post( 'click_number' );

        $res->product_label = $product_label;
        $res->product_status = $request->post( 'product_status' );
        $res->product_weight = $request->post( 'product_weight' );

        #修改商品的原价
        $market_price = $request->post( 'market_price' );


        $good_id = $request->post( 'goods_id' );
        $go_res = Goods::where( 'id' , $good_id )->first();
        if( $request->post( 'good_specs' ) ) {
            $good_specs = json_encode( $request->post( 'good_specs' ) );
            $go_res->good_specs = $good_specs;
        }
        $go_res->market_price = $market_price;
        $go_res->comment_count = $request->post( 'comment_count' );


        if( $res->save() && $go_res->save() ) {

            return redirect()->back();

        } else {

            dd( '修改失败' );

        }


    }


}
