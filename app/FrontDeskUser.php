<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FrontDeskUser extends Model
{
    protected $table = 'fb_user';

    //查询收藏
    public function collection()
    {
        return $this->hasMany( Collection::class , 'user_id' , 'id' );
    }

    //查询评论
    public function comment()
    {
        return $this->hasMany( Comment::class , 'user_id' , 'id' );
    }

    //查询分享
    public function share()
    {
        return $this->hasMany( Share::class , 'user_id' , 'id' );
    }

    //查询关注
    public function following()
    {
        return $this->hasMany( Following::class , 'user_id' , 'id' );
    }

}
