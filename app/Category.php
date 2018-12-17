<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;

class Category extends Base
{
    protected $table = "fb_category";

    protected $thirdFirstHash;

    protected $secondFirstHash;

    protected $categoryThirdAry;

    protected $categoryStatus = 1;

    public function checkExistsById( $id )
    {
        if( !$id ) return false;
        $info = Category::where( 'id' , $id )->get()->count();
        if( $info ) {
            return true;
        }
        else {
            return false;
        }
    }

    public function getThirdCategoryIdByCategoryName($categoryName) {
        if(!$categoryName) return false;
        $categoryInfo = Category::where(['category_name'=>$categoryName,'category_level'=>3])->first(['id']);
        if($categoryInfo) {
            return $categoryInfo->id;
        } else {
            return 0;
        }
    }

    public function getCategoryInfoById( $id )
    {
        if( !$id ) return [];
        $info = Category::where( 'id' , $id )->get();
        return $info;
    }

    public function setCategoryStatus($categoryStatus = 1) {
        $this->categoryStatus = $categoryStatus;
    }

    public function getCategoryAry() {
        if($this->categoryStatus) {
            $categoryList = Category::where(['category_status'=>1])->get(['id','parent_id','category_name','category_level'])->toArray();;
        } else {
            $categoryList = Category::get(['id','parent_id','category_name','category_level'])->toArray();;
        }
        $categoryAry = $category1 = $category2 = $category3 = [];
        if($categoryList) {
            foreach($categoryList as $k => $v) {
                if($v['category_level'] == 1) {
                    $category1[] = $v;
                }
                if($v['category_level'] == 2) {
                    $category2[] = $v;
                }
                if($v['category_level'] == 3) {
                    $category3[] = $v;
                }
            }
            $categoryAry['category1'] = $category1;
            $categoryAry['category2'] = $category2;
            $categoryAry['category3'] = $category3;
        }
        return $categoryAry;
    }

    public function rootCategoryBrandIdsSet($rootCategoryId = 0) {
        $categoryWhere = '';
        if($rootCategoryId) {
            $categoryWhere .= " and root_category_id = '{$rootCategoryId}' ";
        }
        $sql = "select root_category_id,brand_id from fb_product where product_status = 1 and root_category_id != 0 {$categoryWhere} group by root_category_id,brand_id";
        $goodsList = DB::select($sql);
        $categoryBrandHash = [];
        if($goodsList) {
            foreach($goodsList as $k => $v) {
                $categoryBrandHash[$v->root_category_id][] = $v->brand_id;
            }
        }
        if($categoryBrandHash) {
            foreach($categoryBrandHash as $k => $v) {
                $brandIds = implode(',',$v);
                Category::where(['id'=>$k])->update(['brand_ids'=>$brandIds]);
            }    
        }

    }

    private function _categoryFormat() {
        $categoryAry = $this->getCategoryAry();
        $categoryFirst = $categorySecond = $categoryThrid = [];
        $categoryThirdAry = [];
        foreach($categoryAry['category3'] as $k => $v) {
            $info = ['id'=>$v['id'],'name'=>$v['category_name']];
            $categoryThrid[$v['parent_id']][] = $info;
            $categoryThirdAry[] = $v['id'];
        }
        $this->categoryThirdAry = $categoryThirdAry;
        foreach($categoryAry['category2'] as $k => $v) {
            $info = ['id'=>$v['id'],'name'=>$v['category_name'],'children'=>[]];
            $info['children'] = $categoryThrid[$v['id']];
            $categorySecond[$v['parent_id']][] = $info;
        }
        foreach($categoryAry['category1'] as $k => $v) {
            $info = ['id'=>$v['id'],'name'=>$v['category_name'],'children'=>[]];
            $info['children'] = isset($categorySecond[$v['id']]) ? $categorySecond[$v['id']] : [];
            $categoryFirst[] = $info;
        }
        foreach($categoryFirst as $k => $v) {
            foreach($v['children'] as $m => $n) {
                $this->secondFirstHash[$n['id']] = $v['id'];
                foreach($n['children'] as $y => $z) {
                    $this->thirdFirstHash[$z['id']] = $v['id'];
                }
            }
        }
    }

    public function getCategoryThirdAry() {
        if(!$this->categoryThirdAry) {
            $this->_categoryFormat();
        }
        return $this->categoryThirdAry;
    }

    public function getRootCategoryIdBySecondCategoryId($secondCategoryId) {
        if(!$secondCategoryId) return 0;
        if(!$this->secondFirstHash) {
            $this->_categoryFormat();
        }
        return isset($this->secondFirstHash[$thirdCategoryId]) ? $this->secondFirstHash[$thirdCategoryId] : 0;
    }

    public function getRootCategoryIdByThridCategoryId($thirdCategoryId) {
        if(!$thirdCategoryId) return 0;
        if(!$this->thirdFirstHash) {
            $this->_categoryFormat();
        }
        return isset($this->thirdFirstHash[$thirdCategoryId]) ? $this->thirdFirstHash[$thirdCategoryId] : 0;
    }
}
