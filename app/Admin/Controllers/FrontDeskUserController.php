<?php

namespace App\Admin\Controllers;


use App\Brand;
use App\FrontDeskUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class FrontDeskUserController extends Controller
{

    /**
     * 前台用户展示
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function frontDeskUserList( Request $request )
    {
        $search = $request->input();
        $where = [];

        if( isset( $search[ 'name' ] ) ) {
            $where[] = [ 'user_define_nickname' , 'like' , '%' . $search[ 'name' ] . '%' ];
        }

        $userInfo = FrontDeskUser::orderBy( 'created_at' , 'desc' )->withCount( [ 'comment' , 'collection' , 'following' , 'share' ] )->where( $where )->orderBy( 'created_at' , 'desc' )->paginate( 20 );

        if( empty( $search ) ) {
            $search = [
                'name' => ''
            ];
        }
        return view( '/admin/frontDeskUser/list' , compact( 'userInfo' , 'search' ) );
        //        return view('/admin/frontDeskUser/list', compact('userInfo'));
    }

    /**
     * 修改页面
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function frontDeskUserUpdate( \App\FrontDeskUser $frontDeskUser )
    {
        return view( '/admin/frontDeskUser/update' , compact( 'frontDeskUser' ) );
    }

    /**
     * 修改
     *
     * @param Request $request
     *
     * @return array
     */
    public function frontDeskUserSave( Request $request )
    {
        //验证
        $this->validate( $request , [
            'user_status' => 'required' ,
        ] );

        //逻辑
        $data = \request()->all();
        unset( $data[ '_token' ] , $data[ 's' ] );

        $res = FrontDeskUser::where( 'id' , '=' , $data[ 'id' ] )->update( $data );
        if( $res ) {
            return redirect( '/admin/frontDeskUser/list' );
        }
        else {
            callback();
        }
    }

}
