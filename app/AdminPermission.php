<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AdminPermission extends Model
{
    protected $table = "fb_authority";

    // 加上对应的字段
    protected $fillable = [ 'name' , 'description' ];

    /*
     * 权限属于哪些角色
     */
    public function roles()
    {
        return $this->belongsToMany( \App\AdminRole::class , 'fb_role_authority_mapping' , 'authority_id' , 'role_id' )->withPivot( [ 'authority_id' , 'role_id' ] );
    }
}
