<?php

namespace App\Admin\Controllers;

use App\Category;
use App\Author;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;

class AuthorController extends Controller
{
    /**
     * 添加页面
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {

        return view( '/admin/author/index' );

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
            'author_name' => 'required|unique:fb_author' ,
            'author_head_portrait' => 'required'
        ] );

        $res = Author::create( request( [ 'author_name' , 'author_head_portrait' ] ) );

        return redirect( '/admin/author/list' );
    }

    /**
     * 展示
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function AuthorList( Request $request )
    {
        $search = $request->input();
        $where = [];

        if( isset( $search[ 'name' ] ) ) {
            $where[] = [ 'author_name' , 'like' , '%' . $search[ 'name' ] . '%' ];
        }

        //        $where[] = ['category_status', '=', 1];

        $author = Author::orderBy( 'created_at' , 'desc' )->where( $where )->paginate( 20 );
        //接值判断
        if( empty( $search[ 'name' ] ) ) {
            $search[ 'name' ] = '';
        }

        $author->name = $search[ 'name' ];

        return view( '/admin/author/list' , compact( 'author' , 'search' ) );
    }

    /**
     * 修改页面
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function authorUpdate( Author $author )
    {
        $returnUrl = url()->previous();
        return view( '/admin/author/update' , compact( 'author' , 'returnUrl' ) );
    }

    /**
     * 修改
     *
     * @param Request $request
     *
     * @return array
     */
    public function authorSave( Request $request )
    {
        $param = $request->route( 'author' );

        //验证
        $this->validate( $request , [
            'author_name' => "required|unique:fb_author,author_name,$param,id" ,
            //            'author_name' =>Rule::unique('fb_author')->ignore($param, 'id')
        ] );

        //逻辑
        $data = \request()->all();

        if( empty( $data[ 'author_head_portrait' ] ) ) {
            unset( $data[ 'author_head_portrait' ] );
        }

        if( !isset( $data[ 'author_status' ] ) ) {
            $data[ 'author_status' ] = 2;
        }

        $returnUrl = $data[ 'returnUrl' ];

        unset( $data[ '_token' ] , $data[ 's' ] , $data[ 'returnUrl' ] , $data[ 'file' ] );

        $res = Author::where( 'id' , '=' , $data[ 'id' ] )->update( $data );

        if( $res ) {
            return redirect( $returnUrl );
        }
        else {
            callback();
        }
    }


}
