<?php

namespace App\Admin\Controllers;

use App\Website;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WebsiteController extends CommonController
{
    /*
     * 网站列表
     */
    public function index( Request $request )
    {
        $search = $request->input();

        $where = [];

        if( isset( $search[ 'name' ] ) ) {
            $where[] = [ 'website_name' , 'like' , '%' . $search[ 'name' ] . '%' ];
        }

        $website = Website::orderBy( 'created_at' , 'desc' )->with( [ 'country' , 'currency' ] )//            ->where('website_status',1)
        ->where( $where )->paginate( 20 );

        //处理图片
        foreach( $website as $k => $v ) {
            $website[ $k ][ 'website_thumbnail' ] = $this->imageCompatible( $v[ 'website_thumbnail' ] );
        }

        #查询网站最低价商品数量
        $sql = 'select count(*) from fb_website join fb_goods group by product_id having fb_website.website_thumbnail = fb_goods.orignal_website ';

        if( empty( $search ) ) {
            $search = [ 'name' => '' , ];
        }
        //        dd($website);
        return view( '/admin/website/index' , compact( 'website' , 'search' ) );
    }

    /*
     * 创建网站
     */
    public function create()
    {

        $country = DB::table( 'fb_country' )->where( 'place_level' , 1 )->get();

        //        $city=DB::table('fb_country')->where('place_level',2)->get();

        $currency = DB::table( 'fb_currency' )->where( 'is_deleted' , 1 )->get();

        return view( '/admin/website/add' , compact( 'country' , 'currency' ) );
    }

    /*
     * 创建网站行为
     */
    public function store( Request $request )
    {
        $this->validate( $request , [ 'website_name' => 'required|min:3' , 'website_abbreviation' => 'required|min:1' , 'website_url' => 'required|min:3' , 'website_thumbnail' => 'required|min:3' , 'website_country' => 'required' , 'website_currency' => 'required' , 'pay_way' => 'required' ] );
        Website::create( request( [ 'website_name' , 'website_abbreviation' , 'website_url' , 'website_thumbnail' , 'website_country' , 'website_currency' , 'pay_way' ] ) );

        return redirect( '/admin/websites' );
    }


    public function update( Website $website )
    {
        $website->website_thumbnail = $this->imageCompatible( $website->website_thumbnail );
        $country = DB::table( 'fb_country' )->where( 'place_level' , 1 )->get();

        //        $city=DB::table('fb_country')->where('place_level',2)->get();

        $currency = DB::table( 'fb_currency' )->where( 'is_deleted' , 1 )->get();

        return view( '/admin/website/update' , compact( 'website' , 'country' , 'currency' ) );

    }

    /**
     * 执行修改
     *
     * @param Request $request
     * @param Website $website
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function updateDo( Request $request , Website $website )
    {
        $this->validate( $request , [
            'website_name' => 'required|min:3' ,
            'website_url' => 'required|min:3' ,
            'website_thumbnail' => 'required|min:3' ,
            'website_country' => 'required' ,
            'update_type' => 'required' ,
            'website_currency' => 'required' ,
            'pay_way' => 'required'
        ] );

        $id = $request->post( 'id' );
        $res = Website::where( 'id' , $id )->first();
        $res->website_name = $request->post( 'website_name' );
        $res->website_url = $request->post( 'website_url' );
        $res->website_thumbnail = $request->post( 'website_thumbnail' );
        $res->website_country = $request->post( 'website_country' );
        $res->update_type = $request->post( 'update_type' );
        $res->website_currency = $request->post( 'website_currency' );
        $res->website_status = $request->post( 'website_status' );
        $res->pay_way = $request->post( 'pay_way' );

        $website_abbreviation = $request->post( 'website_abbreviation' );
        $update_type = $request->post( 'update_type' );

        //更改网站下的商品更新频率
        DB::table( 'fb_goods' )
            ->where( [ 'orignal_website' => $website_abbreviation ] )
            ->update( [
                'update_type' => $update_type
            ] );

        if( $res->save() ) {
            return redirect( '/admin/websites' );
        } else {
            dd( '修改失败' );
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

        $res = Website::where( "id" , '=' , $data[ 'id' ] )->update( $data );

        dd( $res );

    }


}
