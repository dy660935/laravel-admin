<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;

class GoodsSpecs extends Model
{
    protected $table = "fb_goods_specs";

    public function getGoodsSpecsListByGoodsId($goodsIdAry) {
    	if(!$goodsIdAry || !is_array($goodsIdAry)) return [];
 		$goodsIdStr = implode(',',$goodsIdAry);
    	$sql = "select goods_id,spec_name,is_custom from fb_goods_specs gs left join fb_specs s on gs.spec_id = s.id where goods_id in ({$goodsIdStr})";
    	return DB::select($sql);
    }
}
