<?php

namespace App\Admin\Controllers;

use Illuminate\Http\Request;

class PermissionController extends Controller
{
    /*
     * 权限列表
     */
    public function index()
    {
        $permissions = \App\AdminPermission::paginate( 10 );

        return view( '/admin/permission/index' , compact( 'permissions' ) );
    }

    /*
     * 创建权限
     */
    public function create()
    {
        return view( '/admin/permission/add' );
    }

    /*
     * 创建权限行为
     */
    public function store()
    {
        $this->validate( request() , [
            'name' => 'required|min:3' ,
            'description' => 'required'
        ] );

        \App\AdminPermission::create( request( [ 'name' , 'description' ] ) );

        return redirect( '/admin/permissions' );
    }
}
