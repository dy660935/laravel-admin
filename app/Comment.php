<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    protected $table = "fb_comment";

    public function frontDeskUser()
    {
        return $this->hasOne( FrontDeskUser::class , 'id' , 'user_id' );
    }
}
