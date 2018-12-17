<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CategoryChangeLog extends Model
{
    protected $table = "fb_category_change_log";

    public function checkExistsByWhere( $where )
    {
        if( !$where || !is_array( $where ) ) return [];
        $info = CategoryChangeLog::where( $where )->get()->count();
        if( $info ) {
            return true;
        }
        else {
            return false;
        }
    }
}
