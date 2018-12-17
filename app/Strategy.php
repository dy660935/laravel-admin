<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Strategy extends Base
{
    protected $table = 'fb_strategy';

    public function authorName()
    {
        return $this->hasMany( \App\Author::class , 'author_id' , 'id' );
    }
}
