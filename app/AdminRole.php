<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AdminRole extends Model
{
    protected $table = "fb_roles";

    // 加上对应的字段
    protected $fillable = [ 'name' , 'description' ];

    /*
     * 当前角色的所有权限
     */
    public function permissions()
    {
        return $this->belongsToMany( \App\AdminPermission::class , 'fb_role_authority_mapping' , 'role_id' , 'authority_id' )->withPivot( [ 'authority_id' , 'role_id' ] );
    }

    /*
     * 给角色授权
     */
    public function grantPermission( $permission )
    {
         return $this->permissions()->save( $permission );

    }

    /*
     * 删除role和permission的关联
     */
    public function deletePermission( $permission )
    {
        return $this->permissions()->detach( $permission );
    }

    /*
     * 判断角色是否有权限
     */
    public function hasPermission( $permission )
    {
        return $this->permissions->contains( $permission );
    }
}
