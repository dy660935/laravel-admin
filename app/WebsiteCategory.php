<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WebsiteCategory extends Base
{
    protected $table = "fb_website_category";

    public function getAllWebsiteCategory()
    {
        return WebsiteCategory::get()->toArray();
    }

    public function checkExistsById( $category_id )
    {
        if( !$category_id ) return false;
        $info = WebsiteCategory::where( 'category_id' , $category_id )->get()->count();
        if( $info ) {
            return true;
        }
        else {
            return false;
        }
    }

    public function getCategoryInfoById( $category_id )
    {
        if( !$category_id ) return [];
        $info = WebsiteCategory::where( 'category_id' , $category_id )->get();
        return $info;
    }

    public function checkExistsByWhere( $where )
    {
        if( !$where || !is_array( $where ) ) return false;
        $info = WebsiteCategory::where( $where )->get()->count();
        if( $info ) {
            return true;
        }
        else {
            return false;
        }
    }

    public function getCategoryInfoByWhere( $where )
    {
        if( !$where || !is_array( $where ) ) return [];
        $info = WebsiteCategory::where( $where )->get();
        return $info;
    }
}
