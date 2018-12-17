<?php

namespace App\Admin\Controllers;


use App\Brand;
use App\Category;
use App\Libs\sphinx\SphinxClient;
use App\Website;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class CategoryController extends Controller
{
    /**
     * 添加页面
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $category_data = Category::all();

        return view( '/admin/category/index' , compact( 'category_data' ) );
    }

    public function getHierarchyCategory()
    {
        $data = \request()->all();
        $category_info = Category::where( "category_level" , '=' , $data[ 'category_type' ] )->get();
        $category_info = $category_info->toArray();
        return json_encode( $category_info );
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
        $this->validate( $request , [ 'category_name' => 'required' ] );

        $res = Category::create( request( [ 'category_name' , 'parent_id' , 'category_level' ] ) );

        return redirect( '/admin/category/list' );
    }

    public function CategoryTree( Request $request ) {
        $categoryObj = new Category;
        $categoryAry = $categoryObj->getCategoryAry();
        $categoryFirst = $categorySecond = $categoryThrid = [];

        foreach($categoryAry['category3'] as $k => $v) {
            $info = ['id'=>$v['id'],'name'=>$v['category_name']];
            $categoryThrid[$v['parent_id']][] = $info;
        }
        foreach($categoryAry['category2'] as $k => $v) {
            $info = ['id'=>$v['id'],'name'=>$v['category_name'],'children'=>[]];
            $info['children'] = $categoryThrid[$v['id']];
            $categorySecond[$v['parent_id']][] = $info;
        }
        foreach($categoryAry['category1'] as $k => $v) {
            $info = ['id'=>$v['id'],'name'=>$v['category_name'],'children'=>[]];
            $info['children'] = isset($categorySecond[$v['id']]) ? $categorySecond[$v['id']] : [];
            $categoryFirst[] = $info;
        }

        return view( '/admin/category/tree' , compact( 'categoryFirst' ) );
    }

    /**
     * 展示
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function CategoryList( Request $request )
    {
        $search = $request->input();
        $where = [];

        if( isset( $search[ 'name' ] ) ) {
            $where[] = [ 'category_name' , 'like' , '%' . $search[ 'name' ] . '%' ];
        }
        if( isset( $search[ 'category_level' ] ) ) {
            $where[] = [ 'category_level' , '=' , $search[ 'category_level' ] ];
        }
        //        $where[] = ['category_status', '=', 1];

        $category = Category::orderBy( 'created_at' , 'desc' )->where( $where )->paginate( 20 );

        if( empty( $search ) ) {
            $search = [ 'name' => '' , 'category_level' => '' , ];
        }
        return view( '/admin/category/list' , compact( 'category' , 'search' ) );
    }

    public function setDisplay(Request $request) {
        $id = $request->input('id');
        if(!$id) {
            return [ 'code' => 2 ,'msg'=>'分类不存在'];
        }
        $data['category_status'] = 2;
        $res = Category::where( 'id' , '=' , $id )->update( $data );
        if( $res ) {
            return [ 'code' => 1 ,'msg'=>'成功'];   
        }
        else {
            return [ 'code' => 2 ,'msg'=>'失败'];
        }
    }

    /**
     * 修改页面
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function categoryUpdate( \App\Category $category )
    {
        $category = $category->toArray();
        $category_name = Category::where( 'id' , $category[ 'parent_id' ] )->first();
        $category_data = Category::where( 'parent_id' , $category[ 'parent_id' ] - 1 )->get();
        return view( '/admin/category/update' , compact( 'category' , 'category_data' , 'category_name' ) );
    }

    /**
     * 修改
     *
     * @param Request $request
     *
     * @return array
     */
    public function categorySave( Request $request )
    {
        //验证
        $this->validate( $request , [ 'category_level' => 'required' , 'category_name' => 'required' , ] );

        //逻辑
        $data = \request()->all();

        if( !isset( $data[ 'category_status' ] ) ) {
            $data[ 'category_status' ] = 2;
        }
        unset( $data[ '_token' ] , $data[ 's' ] );
        $res = Category::where( 'id' , '=' , $data[ 'id' ] )->update( $data );
        if( $res ) {
            return redirect( '/admin/category/list' );
        }
        else {
            callback();
        }
    }

    public function categoryMatching( Request $request )
    {

        //DB::connection()->enableQueryLog();#开启执行日志
        $category = DB::table( 'fb_category' )->where( [ 'category_status' => 1 ] )->where( [ 'category_level' => 1 ] )->get();
        //dd(DB::getQueryLog());

        $website_obj = new Website();

        $websiteHash = $website_obj->getWebsiteAry();

        return view( '/admin/category/matching' , compact( 'category' , 'websiteHash' ) );
    }

    public function categoryMatchingDo( Request $request )
    {
        $data = $request->all();

        //        var_dump($data);
        if( $data[ 'website_id' ] != null ) {
            $website_info = explode( ',' , $data[ 'website_id' ] );
        }

        $cl = new SphinxClient();
        //        $cl = new SphinxClient ();
        $mode = isset( $data[ 'mode' ] ) ?
            intval( $data[ 'mode' ] ):
            SPH_MATCH_ALL;
        $cl->SetServer( env('SPHINX_HOST') , 9312 );
        $cl->SetConnectTimeout( 3 );
        $cl->SetArrayResult( true );
        $cl->SetFilter( 'orignal_website_id' , $website_info );
        //        $cl->SetFilter('orignal_website_id',[7,8]);
        $cl->SetMatchMode( $mode );
        //        $cl->SetMatchMode ( 1);
        $cl->SetRankingMode( SPH_RANK_PROXIMITY );
        $cl->SetLimits( 0 , 20 );
        $res = $cl->Query( $data[ 'category_name' ] , env('SPHINX_INDEX_PRODUCT') );
        $str = "";

        if( isset( $res[ 'matches' ] ) ) {

            foreach( $res[ 'matches' ] as $k => $v ) {
                $product_all[] = DB::table( 'fb_product' )
                    ->where( [ 'id' => $v[ 'id' ] ] )
                    ->get( [ 'id' , 'brand_id' , 'category_id' , 'orignal_website_id' ] )
                    ->toArray();
            }

            foreach( $product_all as $k => $v ) {
                foreach( $v as $k1 => $v1 ) {
                    $product_info[] = $v1;
                }
            }
            //            dd($product_info);
            foreach( $product_info as $k => $v ) {

                $product_info1[] = DB::table( 'fb_product as a' )->join( 'fb_brand as b' , 'a.brand_id' , '=' , 'b.id' )->join( 'fb_goods as c' , 'a.original_product_id' , '=' , 'c.original_goods_id' )->join( 'fb_website as d' , 'a.orignal_website_id' , '=' , 'd.id' )->where( [ 'a.id' => $v->id ] )->get( [ 'a.id' , 'category_id' , 'product_image' , 'product_name' , 'original_goods_url' , 'brand_chinese_name' , 'website_name' ] );
            }

            foreach( $product_info1 as $k => $v ) {
                foreach( $v as $k2 => $v2 ) {
                    $product_message[] = $v2;
                }
            }

            $product_message = json_decode( json_encode( $product_message ) , true );

            foreach( $product_message as $k => $v ) {
                if( $v[ 'category_id' ] == 0 ) {
                    $product_message [ $k ] [ 'is_matching' ] = '未匹配';
                }
                else {
                    $product_message [ $k ] [ 'is_matching' ] = '已匹配';
                }
            }

            $str .= "<div class=\"layui-form\">
        <table class=\"layui-table\" lay-skin=\"nob\" style='width: 88%'>
            <tr>
                <td colspan=\"9\">商品信息</td>
            </tr>
            <tr>
                <th style='width: 150px;'><input type='checkbox' class='layui-input' />全选/全不选</th>
                <th>来源网站</th>
                <th>品牌</th>
                <th>商品名称</th>
                <th>商品图片</th>
                <th>匹配状态</th>
            </tr>";

            foreach( $product_message as $k => $v ) {
                $str .= "<tr>
                <td style='width: 80px;'><input type=\"checkbox\" value=\"$v[id]\" class='layui-input
                tiJiao'/></td>
                <td>" . $v[ 'website_name' ] . "</td>
                <td>" . $v[ 'brand_chinese_name' ] . "</td>
                <td>" . $v[ 'product_name' ] . "</td>
                <td>
                    <img src='" . $v[ 'product_image' ] . "' style='width: 50px;height: 50px; ' alt='图片损坏'>
                </td>
                <td>" . $v[ 'is_matching' ] . "</td>  
            </tr>";
            }

            $str .= "</table>
               
            </div>";
        }

        return json_encode( $str );
    }

}
