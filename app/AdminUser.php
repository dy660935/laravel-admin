<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class AdminUser extends Authenticatable
{
    protected $table = 'fb_admin_user';
    protected $rememberTokenName = '';
    use Notifiable;


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name' , 'password' ,
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password' , 'remember_token' ,
    ];

    /*
     * 一个用户有哪些角色
     */
    public function roles()
    {
        return $this->belongsToMany( \App\AdminRole::class , 'fb_admin_role_mapping' , 'admin_id' , 'role_id' )->withPivot( [ 'admin_id' , 'role_id' ] );
    }

    /*
     * 是否有某个角色
     */
    public function isInRoles( $roles )
    {
        return ! !$roles->intersect( $this->roles )->count();
    }

    /*
     * 是否有权限
     */
    public function hasPermission( $permission )
    {
        return $this->isInRoles( $permission->roles );
    }

    /*
     * 给用户分配角色
     */
    public function assignRole( $roleName )
    {
        $role = \App\AdminRole::where( 'name' , $roleName )->first();
        return $this->roles()->save( $role );
    }

    /*
     * 删除user和role的关联
     */
    public function deleteRole( $role )
    {
        return $this->roles()->detach( $role );
    }
}
