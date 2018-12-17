<?php

namespace App\Admin\Controllers;


use App\Comment;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\DB;

class CommentController extends Controller
{
    /**
     * 展示待处理的评论
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function commentList( Request $request )
    {
        $param = $request->route( 'commentStatus' ); //获取当前路径传入的名为id的参数
        $search = $request->input();
        $where = [];
        if( isset( $search[ 'name' ] ) ) {
            $where[] = [ 'comment_title' , 'like' , '%' . $search[ 'name' ] . '%' ];
        }
        if( $param == 2 ) {
            $where[] = [
                'comment_status' , '=' , $param
            ];
        }
        else {
            $where[] = [
                'comment_status' , '!=' , 2
            ];
        }

        //        DB::connection()->enableQueryLog();#开启执行日志

        $userInfo = Comment::orderBy( 'created_at' , 'desc' )->with( [ 'frontDeskUser' => function( $query ) {
            $query->select
            ( 'id' , 'user_define_nickname' );
        }
        ] )->where( $where )
            ->paginate( 20 );

        if( empty( $search ) ) {
            $search = [
                'name' => '' ,
            ];
        }
        if( $param == 2 ) {
            return view( '/admin/comment/index' , compact( 'userInfo' , 'search' , 'param' ) );
            //            return view('/admin/comment/index', compact('userInfo','param'));

        }
        else {
            return view( '/admin/comment/list' , compact( 'userInfo' , 'search' , 'param' ) );
            //            return view('/admin/comment/list', compact('userInfo','param'));

        }
    }

    /**
     * 修改状态如屏蔽、审核成功、恢复
     *
     * @param Request $request
     *
     * @return array
     */
    public function commentSave( Request $request )
    {

        $data = \request()->all();

        unset( $data[ 's' ] );

        $res = Comment::where( 'id' , '=' , $data[ 'id' ] )->update( $data );
        if( $res ) {
            return [ 'font' => '修改成功' , 'code' => 1 ];
        }
        else {
            return [ 'font' => '修改失败' , 'code' => 2 ];
        }
    }
}
