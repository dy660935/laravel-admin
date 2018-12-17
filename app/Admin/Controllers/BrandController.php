<?php

namespace App\Admin\Controllers;


use App\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class BrandController extends CommonController
{
    /**
     * 添加页面
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $country_data = DB::table( 'fb_country' )->where( 'place_level' , 1 )->select( 'id' , 'country' )->get();

        return view( '/admin/brand/index' , compact( 'country_data' ) );
    }


    /**
     * 添加
     *
     * @param Request $request
     *
     * @return array
     */
    public function create( Request $request )
    {
        //验证
        $this->validate( $request , [ 'brand_chinese_name' => 'required|min:2|unique:fb_brand' , 'orginal_brand_logo' => 'required' , 'brand_country' => 'required' , 'brand_describe' => 'required' , 'brand_suitable_genter' => 'required' , 'brand_consumption_level' => 'required' ] );

        $data = \request()->all();

        if( !isset( $data[ 'brand_english_name' ] ) ) {
            $data[ 'brand_english_name' ] = '';
        }
        //        $data['orginal_brand_logo']=$data['own_brand_logo'];
        //        dd($data);
        //        DB::connection()->enableQueryLog();#开启执行日志
        $res = Brand::create( request( [ 'brand_chinese_name' , 'brand_english_name' , 'brand_country' , 'brand_describe' , 'brand_suitable_genter' , 'brand_consumption_level' , 'orginal_brand_logo' ] ) );
        //        dd(DB::getQueryLog());
        //        dd($res);

        if( $res ) {
            return redirect( '/admin/brand/list' );
        }
        else {
            callback();
        }
    }

    /**
     * 展示
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function brandList( Request $request )
    {
        $search = $request->input();
        $where = [];
        $english_where = [];
        if( isset( $search[ 'name' ] ) ) {
            $where[] = [ 'brand_chinese_name' , 'like' , '%' . $search[ 'name' ] . '%' ];
            $english_where[] = [ 'brand_english_name' , 'like' , '%' . $search[ 'name' ] . '%' ];
        }
        if( isset( $search[ 'country' ] ) ) {

            $where[] = [ 'country' , 'like' , '%' . $search[ 'country' ] . '%' ];
            $english_where[] = [ 'country' , 'like' , '%' . $search[ 'country' ] . '%' ];

        }
        if( isset( $search[ 'status' ] ) ) {

            $where[] = [ 'brand_status' , '=' , $search[ 'status' ] ];
            $english_where[] = [ 'brand_status' , '=' , $search[ 'status' ] ];
        }
        //        if(!isset($search['country']) && isset($search['name'])){
        //            $english_where[] = ['brand_status', '=', $search['status']];
        //        }
        //        DB::connection()->enableQueryLog();#开启执行日志

        $data = \App\Country::join( 'fb_brand' , 'fb_country.id' , '=' , 'brand_country' )->where( $where )->orWhere( $english_where )->orderBy( 'brand_weight' , 'desc' )->paginate( 20 );

        foreach( $data as $k => $v ) {

            $data[ $k ][ 'product_num' ] = DB::table( 'fb_product' )->where( 'brand_id' , '=' , $v[ 'id' ] )->count();
            $data[ $k ][ 'strategy_num' ] = DB::table( 'fb_strategy' )->where( 'strategy_label' , 'like' , '%' . $v[ 'brand_chinese_name' ] . '%' )->count();

            //处理图片
            $data[ $k ][ 'orginal_brand_logo' ] = $this->imageCompatible( $v[ 'orginal_brand_logo' ] );
        }
        //        dd($data);

        //        $data = \App\Country::join('fb_brand', 'fb_country.id', '=', 'brand_country')->where($where)->paginate(1);
        //        print_r(DB::getQueryLog());
        if( empty( $search ) ) {
            $search = [ 'name' => '' , 'country' => '' , 'status' => '' ];
        }
        return view( '/admin/brand/list' , compact( 'data' , 'search' ) );
    }

    /**
     * 修改页面
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function brandUpdate( \App\Brand $brand )
    {

        $brand->orginal_brand_logo = $this->imageCompatible( $brand->orginal_brand_logo );

        $country = DB::table( 'fb_country' )->where( 'place_level' , 1 )->get();

        $city = DB::table( 'fb_country' )->where( 'place_level' , 2 )->get();

        $currency = DB::table( 'fb_currency' )->where( 'is_deleted' , 1 )->get();

        $returnUrl = url()->previous();

        return view( '/admin/brand/update' , compact( 'brand' , 'country' , 'city' , 'currency' , 'returnUrl' ) );
    }

    /**
     * 修改
     *
     * @param Request $request
     *
     * @return array
     */
    public function brandSave( Request $request )
    {
        //验证
        $this->validate( $request , [ 'brand_chinese_name' => 'required|min:2' , 'orginal_brand_logo' => 'required' , 'brand_country' => 'required' , 'brand_suitable_genter' => 'required' , 'brand_consumption_level' => 'required' , 'is_hot' => 'required' , 'brand_status' => 'required' ] );

        //逻辑
        $data = \request()->all();

        $url = $request->post( 'returnUrl' );

        if( $data[ 'orginal_brand_logo' ] == "" ) {
            unset( $data[ 'orginal_brand_logo' ] );
        }

        unset( $data[ 'file' ] , $data[ '_token' ] , $data[ 's' ] , $data[ 'returnUrl' ] );

        $res = Brand::where( 'id' , '=' , $data[ 'id' ] )->update( $data );

        if( $res ) {
            return redirect( $url );
        }
        else {
            callback();
        }
    }

    /**
     * 备注修改
     *
     * @param Request $request
     */
    public function updateRemark( Request $request )
    {
        //逻辑
        $data = $request->all();

        $url = $data[ 'url' ];

        unset( $data[ 's' ] , $data[ 'url' ] );

        $res = Brand::where( "id" , '=' , $data[ 'id' ] )->update( $data );

        dd( $res );

    }

}
