<?php

namespace App\Admin\Controllers;

use App\Author;
use App\Strategy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StrategyController extends CommonController
{
    /**
     * 添加页面
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {

        $author_obj = new Author();
        $author_information = $author_obj->authorSelect();

        $category_information = DB::table( 'fb_category' )
            ->where( [ 'category_status' => 1 , 'parent_id' => 0 , 'category_level' => 1 ] )
            ->select( 'id' , 'category_name' )
            ->get();

        return view( '/admin/strategy/index' , compact( 'author_information' , 'category_information' ) );
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
        $this->validate( $request , [
                'strategy_title' => 'required|min:2' ,
                'author_id' => 'required' ,
                'category_id' => 'required' ,
                'strategy_label' => 'required' ,
                'strategy_image' => 'required' ,
                'strategy_slider_image' => 'required' ,
                'strategy_abstract' => 'required' ,
                'strategy_weight' => 'required|max:8' ,
                'strategy_describe' => 'required'
            ]
        );
        //逻辑
        $data = \request()->all();

        if( $data[ 'strategy_label' ] ) {
            $data[ 'strategy_label' ] = implode( ',' , $data[ 'strategy_label' ] );
        }

        if( !isset( $data[ 'is_hot' ] ) ) {
            $data[ 'is_hot' ] = 2;
        }

        $strategy_id = DB::table( 'fb_strategy' )->insertGetId( [
            'strategy_title' => $data[ 'strategy_title' ] ,
            'author_id' => $data[ 'author_id' ] ,
            'strategy_label' => $data[ 'strategy_label' ] ,
            'strategy_image' => $data[ 'strategy_image' ] ,
            'strategy_describe' => $data[ 'strategy_describe' ] ,
            'strategy_abstract' => $data[ 'strategy_abstract' ] ,
            'is_hot' => $data[ 'is_hot' ] ,
            'strategy_weight' => $data[ 'strategy_weight' ] ,
            'strategy_slider_image' => $data[ 'strategy_slider_image' ] ,
            'is_weChat_add' => 1 ,
            'created_at' => date( 'Y-m-d H:i:s' , time() ) ,
        ] );

        if( $strategy_id ) {
            $res = DB::table( 'fb_category_strategy_mapping' )
                ->insert( [
                    'category_id' => $data[ 'category_id' ] ,
                    'strategy_id' => $strategy_id ,
                    'created_at' => date( 'Y-m-d H:i:s' , time() )
                ] );
        }

        if( $res ) {
            return redirect( '/admin/strategy/list' );
        }
    }

    /**
     * 攻略导入页面
     */
    public function strategyImport()
    {

        $author_obj = new Author();
        $author_information = $author_obj->authorSelect();

        $category_information = DB::table( 'fb_category' )
            ->where( [ 'category_status' => 1 , 'parent_id' => 0 , 'category_level' => 1 ] )
            ->select( 'id' , 'category_name' )
            ->get();
        return view( '/admin/strategy/import' , compact( 'author_information' , 'category_information' ) );
    }

    /**
     * 获取微信公众号文章（标题、作者、内容）
     *
     * @param Request $request
     *
     * @return string
     */
    public function getImportInfo( Request $request )
    {

        $this->validate( $request , [ 'importName' => 'required|active_url' , ] , [ 'importName.required' => '链接地址不能为空' , 'importName.active_url' => '链接格式不正确,正确格式为http://www.***.com或者http://www.***.cn' , ] );

        $data = \request()->all();

        header( 'content-type:text/html; charset=UTF-8' );
        //获取地址以及数据
        $url = "$data[importName]";

        $data = file_get_contents( $url );

        //PHP正则
        $preg1 = '#<title>(.*)</title>#isU';
        $preg3 = '#<strong class="profile_nickname">(.*)</strong>#isU';

        //获悉入库的数据整理
        preg_match( $preg1 , $data , $title_result );
        preg_match( $preg3 , $data , $author_result );

        $strategy_info[ 'strategy_title' ] = trim( $title_result[ 1 ] );
        $strategy_info[ 'author_name' ] = trim( $author_result[ 1 ] );
        $strategy_info[ 'strategy_wechat_url' ] = $url;

        return json_encode( $strategy_info );
    }

    /**
     * 微信公众号文章添加入库
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function strategyImportAdd( Request $request )
    {
        //验证
        $this->validate( $request , [
            'strategy_title' => 'required|min:2' ,
            'author_name' => 'required' ,
            'category_id' => 'required' ,
            'strategy_label' => 'required' ,
            'strategy_image' => 'required' ,
            'strategy_slider_image' => 'required' ,
            'strategy_wechat_url' => 'required' ,
            'strategy_weight' => 'required||max:8'
        ] );
        //逻辑
        $data = \request()->all();

        if( $data[ 'strategy_label' ] ) {
            $data[ 'strategy_label' ] = implode( ',' , $data[ 'strategy_label' ] );
        }

        if( !isset( $data[ 'strategy_clicks' ] ) ) {
            $data[ 'strategy_clicks' ] = 0;
        }

        if( !isset( $data[ 'strategy_daily_clicks' ] ) ) {
            $data[ 'strategy_daily_clicks' ] = 0;
        }

        if( !isset( $data[ 'is_hot' ] ) ) {
            $data[ 'is_hot' ] = 2;
        }

        //
        //        if (!isset($data['share_number'])) {
        //            $data['share_number'] = 0;
        //        }
        //
        //        if (!isset($data['comment_number'])) {
        //            $data['comment_number'] = 0;
        //        }

        $where = [];
        $where[] = [ 'author_name' , 'like' , '%' . $data[ 'author_name' ] . '%' ];
        //        DB::connection()->enableQueryLog();#开启执行日志
        $res = Author::orderBy( 'created_at' , 'desc' )->where( $where )->get()->toArray();
        //            print_r(DB::getQueryLog());

        if( empty( $res ) ) {
            DB::table( 'fb_author' )->insert( [ 'author_name' => $data[ 'author_name' ] , 'author_head_portrait' => '' ] );
            $data[ 'author_id' ] = DB::getPdo()->lastInsertId();
        } else {
            foreach( $res as $k => $v ) {
                $data[ 'author_id' ] = $v[ 'id' ];
            }
        }

        $strategy_id = DB::table( 'fb_strategy' )->insertGetId( [
            'strategy_title' => $data[ 'strategy_title' ] ,
            'author_id' => $data[ 'author_id' ] ,
            'strategy_label' => $data[ 'strategy_label' ] ,
            'strategy_image' => $data[ 'strategy_image' ] ,
            'strategy_clicks' => $data[ 'strategy_clicks' ] ,
            'strategy_daily_clicks' => $data[ 'strategy_daily_clicks' ] ,
            'strategy_weight' => $data[ 'strategy_weight' ] ,
            //            'collection_number' => $data[ 'collection_number' ] ,
            //            'share_number' => $data[ 'share_number' ] ,
            //            'comment_number' => $data[ 'comment_number' ] ,
            'strategy_wechat_url' => $data[ 'strategy_wechat_url' ] ,
            'strategy_abstract' => $data[ 'strategy_abstract' ] ,
            'strategy_slider_image' => $data[ 'strategy_slider_image' ] ,
            'is_hot' => $data[ 'is_hot' ] ,
            'is_weChat_add' => 2 ,
            'created_at' => date( 'Y-m-d H:i:s' , time() ) ,
        ] );

        if( $strategy_id ) {
            $res = DB::table( 'fb_category_strategy_mapping' )
                ->insert( [
                    'category_id' => $data[ 'category_id' ] ,
                    'strategy_id' => $strategy_id ,
                    'created_at' => date( 'Y-m-d H:i:s' , time() )
                ] );
        }

        if( $res ) {
            return redirect( '/admin/strategy/list' );
        }
    }

    /**
     * 展示
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function strategyList( Request $request )
    {
        $search = $request->input();
        $where = [];

        if( isset( $search[ 'name' ] ) ) {
            $where[] = [ 'strategy_title' , 'like' , '%' . $search[ 'name' ] . '%' ];
        }

        //        $where[] = ['strategy_status', '=', 1];

        $data = \App\Author::join( 'fb_strategy' , 'fb_author.id' , '=' , 'fb_strategy.author_id' )
            ->leftJoin( 'fb_category_strategy_mapping as a' , 'fb_strategy.id' , '=' , 'a.strategy_id' )
            ->leftJoin( 'fb_category as b' , 'a.category_id' , '=' , 'b.id' )
            ->where( $where )
            ->orderBy( 'fb_strategy.strategy_weight' , 'desc' )
            ->select(
                'fb_strategy.id' ,
                'author_name' ,
                'strategy_title' ,
                'strategy_clicks' ,
                'strategy_daily_clicks' ,
                'strategy_label' ,
                'strategy_status' ,
                'strategy_weight' ,
                'is_hot' ,
                'category_name'
            )
            ->paginate( 20 );
        //        dd($data);
        if( empty( $search ) ) {
            $search = [ 'name' => '' ];
        }
        return view( '/admin/strategy/list' , compact( 'data' , 'search' ) );
    }

    /**
     * 修改页面
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function strategyUpdate( \App\Strategy $strategy )
    {
        $returnUrl = url()->previous();

        $author_obj = new Author();
        $author_information = $author_obj->authorSelect();

        $category_information = DB::table( 'fb_category' )
            ->where( [ 'category_status' => 1 , 'parent_id' => 0 , 'category_level' => 1 ] )
            ->select( 'id' , 'category_name' )
            ->get();

        $strategy = $strategy->toArray();

        $category_id = DB::table( 'fb_category_strategy_mapping' )
            ->where( [ 'strategy_id' => $strategy[ 'id' ] ] )
            ->select( 'category_id' )
            ->first();

        if( $category_id != "" ) {
            $strategy[ 'category_id' ] = $category_id->category_id;
        } else {
            $strategy[ 'category_id' ] = '';
        }

        $strategy[ 'strategy_label' ] = explode( ',' , $strategy[ 'strategy_label' ] );
        $strategy[ 'strategy_image' ] = $this->imageCompatible( $strategy[ 'strategy_image' ] );
        $strategy[ 'strategy_slider_image' ] = $this->imageCompatible( $strategy[ 'strategy_slider_image' ] );
        //        dd($strategy);
        return view( '/admin/strategy/update' , compact( 'strategy' , 'author_information' , 'category_information' , 'returnUrl' ) );
    }

    /**
     * 修改
     *
     * @param Request $request
     *
     * @return array
     */
    public function strategySave( Request $request )
    {
        //验证
        $this->validate( $request , [
            'strategy_title' => 'required|min:2' ,
            'author_id' => 'required' ,
            'category_id' => 'required' ,
            'strategy_label' => 'required' ,
            'strategy_abstract' => 'required' ,
            'strategy_weight' => 'required|max:8' ,
        ] );

        //逻辑
        $data = \request()->all();
        //        dd( $data );
        if( $data[ 'strategy_label' ] ) {
            $data[ 'strategy_label' ] = implode( ',' , $data[ 'strategy_label' ] );
        }

        if( !isset( $data[ 'strategy_image' ] ) ) {
            unset( $data[ 'strategy_image' ] );
        }

        if( !isset( $data[ 'strategy_slider_image' ] ) ) {
            unset( $data[ 'strategy_slider_image' ] );
        }

        if( !isset( $data[ 'strategy_status' ] ) ) {
            $data[ 'strategy_status' ] = 2;
        }

        if( !isset( $data[ 'is_hot' ] ) ) {
            $data[ 'is_hot' ] = 2;
        }

        $returnUrl = $data[ 'returnUrl' ];
        $category_id = $data[ 'category_id' ];
        //        dd($returnUrl);
        unset( $data[ 'file' ] , $data[ '_token' ] , $data[ 's' ] , $data[ 'returnUrl' ] , $data[ 'category_id' ] );

        $res = Strategy::where( 'id' , '=' , $data[ 'id' ] )->update( $data );

        $mapping = [
            'category_id' => $category_id ,
            'strategy_id' => $data[ 'id' ]
        ];

        DB::table( 'fb_category_strategy_mapping' )->updateOrInsert( [ 'strategy_id' => $data[ 'id' ] ] , $mapping );

        //渲染
        if( $res ) {
            return redirect( $returnUrl );
        } else {
            callback();
        }
    }

    /**
     * 详情
     *
     * @param Strategy $strategy
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function strategyDetail( \App\Strategy $strategy )
    {
        $strategy = $strategy->toArray();

        $data = DB::table( 'fb_strategy as a' )
            ->join( 'fb_author as b' , 'b.id' , '=' , 'a.author_id' )
            ->join( 'fb_category_strategy_mapping as c' , 'a.id' , '=' , 'c.strategy_id' )
            ->join( 'fb_category as d' , 'c.category_id' , '=' , 'd.id' )
            ->where( 'a.id' , $strategy[ 'id' ] )
            ->select(
                'a.id' ,
                'strategy_title' ,
                'strategy_image' ,
                'strategy_weight' ,
                'strategy_wechat_url' ,
                'strategy_abstract' ,
                'strategy_describe' ,
                'strategy_label' ,
                'is_hot' ,
                'strategy_status' ,
                'strategy_slider_image' ,
                'author_name' ,
                'category_name'
            )
            ->first();

        $data = get_object_vars( $data );


        $data[ 'strategy_label' ] = explode( ',' , $data[ 'strategy_label' ] );
        $data[ 'strategy_image' ] = $this->imageCompatible( $data[ 'strategy_image' ] );
        $data[ 'strategy_slider_image' ] = $this->imageCompatible( $data[ 'strategy_slider_image' ] );

        return view( '/admin/strategy/detail' , compact( 'data' ) );
    }
}
